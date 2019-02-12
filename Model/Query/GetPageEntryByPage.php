<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Resolver\StoreIdsByStoreCodeResolver;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\Exception\NoSuchEntityException;

class GetPageEntryByPage
{
    /**
     * @var GetPageEntryList
     */
    private $getPageEntryList;

    /**
     * @var StoreIdsByStoreCodeResolver
     */
    private $storeIdsByStoreCodeResolver;

    /**
     * @var HasStoreMatches
     */
    private $hasStoreMatches;

    /**
     * @param GetPageEntryList $getPageEntryList
     * @param StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver
     * @param HasStoreMatches $hasStoreMatches
     */
    public function __construct(
        GetPageEntryList $getPageEntryList,
        StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver,
        HasStoreMatches $hasStoreMatches
    ) {
        $this->getPageEntryList = $getPageEntryList;
        $this->storeIdsByStoreCodeResolver = $storeIdsByStoreCodeResolver;
        $this->hasStoreMatches = $hasStoreMatches;
    }

    /**
     * @param PageInterface $page
     * @return PageEntryInterface|null
     */
    public function execute(PageInterface $page): ?PageEntryInterface
    {
        foreach ($this->getPageEntryList->get() as $entry) {
            if ($page->getIdentifier() !== $entry->getIdentifier()) {
                continue;
            }

            try {
                $entryStoreIds = $this->storeIdsByStoreCodeResolver->execute($entry->getStores());
                $pageStoreIds = $this->retrieveStoreIds($page);
                if ($this->hasStoreMatches->execute($entryStoreIds, $pageStoreIds)) {
                    return $entry;
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * @param PageInterface $page
     * @return int[]
     */
    public function retrieveStoreIds(PageInterface $page)
    {
        if ($page instanceof Page) {
            return $page->getStores();
        }

        return [];
    }
}
