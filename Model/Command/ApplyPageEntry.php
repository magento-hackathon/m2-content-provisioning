<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Query\GetFirstPageByPageEntry;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\Data\PageInterfaceFactory;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ApplyPageEntry
{
    /**
     * @var PageInterfaceFactory
     */
    private $pageFactory;

    /**
     * @var GetFirstPageByPageEntry
     */
    private $getFirstPageByPageEntry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param PageInterfaceFactory $pageFactory
     * @param GetFirstPageByPageEntry $getFirstPageByPageEntry
     * @param PageRepositoryInterface $pageRepository
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        PageInterfaceFactory $pageFactory,
        GetFirstPageByPageEntry $getFirstPageByPageEntry,
        PageRepositoryInterface $pageRepository,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->getFirstPageByPageEntry = $getFirstPageByPageEntry;
        $this->logger = $logger;
        $this->pageRepository = $pageRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param PageEntryInterface $pageEntry
     * @throws LocalizedException
     */
    public function execute(PageEntryInterface $pageEntry): void
    {
        try {
            $page = $this->getFirstPageByPageEntry->execute($pageEntry);
            if ($page === null) {
                /** @var PageInterface $page */
                $page = $this->pageFactory->create([]);
            }
            $page->addData($this->normalizeData($pageEntry->getData()));
            $this->pageRepository->save($page);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws NoSuchEntityException
     */
    private function normalizeData(array $data): array
    {
        $storeIds = [];
        $storeCodes = $data['stores'] ?? [];
        foreach ($storeCodes as $code) {
            $storeIds[] = $this->storeManager->getStore($code)->getId();
        }
        $data['stores'] = $storeIds;
        return $data;
    }
}