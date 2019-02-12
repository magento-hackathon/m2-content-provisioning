<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Resolver\StoreIdsByStoreCodeResolver;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GetPagesByPageEntry
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var StoreIdsByStoreCodeResolver
     */
    private $storeIdsByStoreCodeResolver;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PageRepositoryInterface $pageRepository
     * @param StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PageRepositoryInterface $pageRepository,
        StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pageRepository = $pageRepository;
        $this->storeIdsByStoreCodeResolver = $storeIdsByStoreCodeResolver;
    }

    /**
     * @param PageEntryInterface $pageEntry
     * @return PageInterface[]
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(PageEntryInterface $pageEntry): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('identifier', $pageEntry->getIdentifier())
            ->addFilter('store_id', $this->storeIdsByStoreCodeResolver->execute($pageEntry->getStores()))
            ->create();
        $searchResult = $this->pageRepository->getList($searchCriteria);
        return $searchResult->getItems();
    }
}
