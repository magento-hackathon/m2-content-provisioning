<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Model\Resolver\StoreIdsByStoreCodeResolver;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GetBlocksByBlockEntry
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StoreIdsByStoreCodeResolver
     */
    private $storeIdsByStoreCodeResolver;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeIdsByStoreCodeResolver = $storeIdsByStoreCodeResolver;
    }

    /**
     * @param BlockEntryInterface $pageEntry
     * @return BlockInterface[]
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(BlockEntryInterface $pageEntry): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('identifier', $pageEntry->getIdentifier())
            ->addFilter('store_id', $this->storeIdsByStoreCodeResolver->execute($pageEntry->getStores()))
            ->create();
        $searchResult = $this->blockRepository->getList($searchCriteria);
        return $searchResult->getItems();
    }
}
