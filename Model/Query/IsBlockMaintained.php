<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class IsBlockMaintained
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var GetBlockEntryByBlock
     *
     */
    private $getBlockEntryByBlock;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param GetBlockEntryByBlock $getBlockEntryByBlock
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        GetBlockEntryByBlock $getBlockEntryByBlock
    ) {

        $this->blockRepository = $blockRepository;
        $this->getBlockEntryByBlock = $getBlockEntryByBlock;
    }

    /**
     * @param int $entityId
     * @return bool
     */
    public function get(int $entityId): bool
    {
        try {
            $page = $this->blockRepository->getById($entityId);
            $entry = $this->getBlockEntryByBlock->execute($page);
            return $entry && $entry->isMaintained();
        } catch (LocalizedException $noSuchEntityException) {
            return false;
        }
    }
}
