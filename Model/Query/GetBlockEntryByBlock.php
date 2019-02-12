<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Model\Resolver\StoreIdsByStoreCodeResolver;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\Block;
use Magento\Framework\Exception\NoSuchEntityException;

class GetBlockEntryByBlock
{
    /**
     * @var GetBlockEntryList
     */
    private $getBlockEntryList;

    /**
     * @var StoreIdsByStoreCodeResolver
     */
    private $storeIdsByStoreCodeResolver;

    /**
     * @var HasStoreMatches
     */
    private $hasStoreMatches;

    /**
     * @param GetBlockEntryList $getBlockEntryList
     * @param StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver
     * @param HasStoreMatches $hasStoreMatches
     */
    public function __construct(
        GetBlockEntryList $getBlockEntryList,
        StoreIdsByStoreCodeResolver $storeIdsByStoreCodeResolver,
        HasStoreMatches $hasStoreMatches
    ) {
        $this->getBlockEntryList = $getBlockEntryList;
        $this->storeIdsByStoreCodeResolver = $storeIdsByStoreCodeResolver;
        $this->hasStoreMatches = $hasStoreMatches;
    }

    /**
     * @param BlockInterface $block
     * @return BlockEntryInterface|null
     */
    public function execute(BlockInterface $block): ?BlockEntryInterface
    {
        foreach ($this->getBlockEntryList->get() as $entry) {
            if ($block->getIdentifier() !== $entry->getIdentifier()) {
                continue;
            }

            try {
                $entryStoreIds = $this->storeIdsByStoreCodeResolver->execute($entry->getStores());
                $entityStoreIds = $this->retrieveStoreIds($block);
                if ($this->hasStoreMatches->execute($entryStoreIds, $entityStoreIds)) {
                    return $entry;
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * @param BlockInterface $block
     * @return array
     */
    public function retrieveStoreIds(BlockInterface $block): array
    {
        if ($block instanceof Block) {
            return $block->getStores();
        }

        return [];
    }
}
