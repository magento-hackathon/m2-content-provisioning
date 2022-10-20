<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class HasDefaultBlockConfiguration
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var GetBlockEntryByBlock
     */
    private $getBlockEntryByBlock;

    /**
     * @param BlockRepositoryInterface $pageRepository
     * @param GetBlockEntryByBlock $getPageEntryByPage
     */
    public function __construct(
        BlockRepositoryInterface $pageRepository,
        GetBlockEntryByBlock $getPageEntryByPage
    ) {
        $this->blockRepository = $pageRepository;

        $this->getBlockEntryByBlock = $getPageEntryByPage;
    }

    /**
     * @param int $entityId
     * @return bool
     */
    public function execute(int $entityId): bool
    {
        try {
            $block = $this->blockRepository->getById($entityId);
            $entry = $this->getBlockEntryByBlock->execute($block);
            return $entry ? true : false;
        } catch (LocalizedException $noSuchEntityException) {
            return false;
        }
    }
}
