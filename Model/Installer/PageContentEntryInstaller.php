<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Installer;

use Firegento\ContentProvisioning\Api\ContentEntryInstallerInterface;
use Firegento\ContentProvisioning\Api\Data\ContentEntryInterface;
use Magento\Cms\Api\GetPageByIdentifierInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class PageContentEntryInstaller implements ContentEntryInstallerInterface
{
    /**
     * @var GetPageByIdentifierInterface
     */
    private $getPageByIdentifier;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param GetPageByIdentifierInterface $getPageByIdentifier
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param PageFactory $pageFactory
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        GetPageByIdentifierInterface $getPageByIdentifier,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepository
    ) {
        $this->getPageByIdentifier = $getPageByIdentifier;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param ContentEntryInterface $contentEntry
     * @throws LocalizedException
     */
    public function install(ContentEntryInterface $contentEntry): void
    {
        $representations = $this->findRepresentations($contentEntry);
        if ($this->hasMultipleRepresentations($representations)) {
            $this->logger->error(__(
                'This content entry (%type - %identifier) present for stores (%stores) in different versions!',
                [
                    'type' => $contentEntry->getType(),
                    'identifier' => $contentEntry->getIdentifier(),
                    'stores' => implode(', ', $contentEntry->getStores()),
                ]
            ));
        }

        if (!$this->canPersist($representations, $contentEntry)) {
            return;
        }

        /** @var Page $model */
        $model = $this->pageFactory->create([]);
        if ($this->canUpdate($representations, $contentEntry)) {
            $model = array_shift($representations);
        }

        $this->applyChanges($model, $contentEntry);
        $this->pageRepository->save($model);
    }

    /**
     * @param Page $page
     * @param ContentEntryInterface $contentEntry
     * @throws NoSuchEntityException
     */
    private function applyChanges(Page $page, ContentEntryInterface $contentEntry): void
    {
        $page->setContent($contentEntry->getContent());
        $page->setIdentifier($contentEntry->getIdentifier());
        $page->setStoreId($this->retrieveStoreIds($contentEntry->getStores()));
    }

    /**
     * @param array $storeCodes
     * @return array
     * @throws NoSuchEntityException
     */
    private function retrieveStoreIds(array $storeCodes)
    {
        $ids = [];
        foreach ($storeCodes as $code) {
            $ids[] = $this->storeManager->getStore($code)->getId();
        }
        return $ids;
    }

    /**
     * @param array $representations
     * @param ContentEntryInterface $contentEntry
     * @return bool
     */
    private function canPersist(array $representations, ContentEntryInterface $contentEntry)
    {
        return count($representations) === 0 || $this->canUpdate($representations, $contentEntry);
    }

    /**
     * @param array $representations
     * @param ContentEntryInterface $contentEntry
     * @return bool
     */
    private function canUpdate(array $representations, ContentEntryInterface $contentEntry): bool
    {
        return count($representations) === 1
            && $contentEntry->isMaintained();
    }

    /**
     * @param ContentEntryInterface $contentEntry
     * @return array
     */
    private function findRepresentations(ContentEntryInterface $contentEntry): array
    {
        $presentIn = [];
        foreach ($contentEntry->getStores() as $storeCode) {
            try {
                $store = $this->storeManager->getStore($storeCode);
                $page = $this->getPageByIdentifier->execute($contentEntry->getIdentifier(), (int)$store->getId());
                $presentIn[$page->getId()] = $page;
            } catch (NoSuchEntityException $exception) {
            }
        }

        return $presentIn;
    }

    /**
     * @param array $representations
     * @return bool
     */
    private function hasMultipleRepresentations(array $representations): bool
    {
        return count($representations) > 1;
    }
}