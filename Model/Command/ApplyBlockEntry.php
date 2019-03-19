<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Model\Query\GetFirstBlockByBlockEntry;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

class ApplyBlockEntry
{
    /**
     * @var BlockInterfaceFactory
     */
    private $blockFactory;

    /**
     * @var GetFirstBlockByBlockEntry
     */
    private $getFirstBlockByBlockEntry;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var NormalizeData
     */
    private $normalizeData;

    /**
     * @param BlockInterfaceFactory $blockFactory
     * @param GetFirstBlockByBlockEntry $getFirstBlockByBlockEntry
     * @param BlockRepositoryInterface $blockRepository
     * @param NormalizeData $normalizeData
     */
    public function __construct(
        BlockInterfaceFactory $blockFactory,
        GetFirstBlockByBlockEntry $getFirstBlockByBlockEntry,
        BlockRepositoryInterface $blockRepository,
        NormalizeData $normalizeData
    ) {
        $this->blockFactory = $blockFactory;
        $this->getFirstBlockByBlockEntry = $getFirstBlockByBlockEntry;
        $this->blockRepository = $blockRepository;
        $this->normalizeData = $normalizeData;
    }

    /**
     * @param BlockEntryInterface $blockEntry
     * @throws LocalizedException
     */
    public function execute(BlockEntryInterface $blockEntry): void
    {
        $block = $this->getFirstBlockByBlockEntry->execute($blockEntry);
        if ($block === null) {
            /** @var BlockInterface $page */
            $block = $this->blockFactory->create([]);
        }
        $block->addData($this->normalizeData->execute($blockEntry->getData()));
        $this->blockRepository->save($block);
    }
}
