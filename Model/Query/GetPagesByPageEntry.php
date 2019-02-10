<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\GetPageByIdentifierInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class GetPagesByPageEntry
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
     * @param GetPageByIdentifierInterface $getPageByIdentifier
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GetPageByIdentifierInterface $getPageByIdentifier,
        StoreManagerInterface $storeManager
    ) {
        $this->getPageByIdentifier = $getPageByIdentifier;
        $this->storeManager = $storeManager;
    }

    /**
     * @param PageEntryInterface $pageEntry
     * @return PageInterface[]
     * @throws NoSuchEntityException
     */
    public function execute(PageEntryInterface $pageEntry): array
    {
        $pages = [];
        foreach ($pageEntry->getStores() as $storeCode) {
            $store = $this->storeManager->getStore($storeCode);
            try {
                $page = $this->getPageByIdentifier->execute($pageEntry->getIdentifier(), (int)$store->getId());
                $pages[] = $page;
            } catch (NoSuchEntityException $exception) {
                // This exception is expected and do not need be handled
                // @TODO Create own page loader class
            }
        }
        return $pages;
    }
}
