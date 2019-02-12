<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GetFirstBlockByBlockEntry
{
    /**
     * @var GetBlocksByBlockEntry
     */
    private $getBlocksByBlockEntry;

    /**
     * @param GetBlocksByBlockEntry $getBlocksByBlockEntry
     */
    public function __construct(
        GetBlocksByBlockEntry $getBlocksByBlockEntry
    ) {
        $this->getBlocksByBlockEntry = $getBlocksByBlockEntry;
    }

    /**
     * @param BlockEntryInterface $blockEntry
     * @return BlockInterface|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(BlockEntryInterface $blockEntry): ?BlockInterface
    {
        $pages = $this->getBlocksByBlockEntry->execute($blockEntry);
        return array_shift($pages);
    }
}
