<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Validator;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Query\GetFirstBlockByBlockEntry;
use Magento\Framework\Exception\NoSuchEntityException;

class CanApplyBlockEntry
{
    /**
     * @var GetFirstBlockByBlockEntry
     */
    private $getFirstBlockByBlockEntry;

    /**
     * @param GetFirstBlockByBlockEntry $getFirstBlockByBlockEntry
     */
    public function __construct(
        GetFirstBlockByBlockEntry $getFirstBlockByBlockEntry
    ) {
        $this->getFirstBlockByBlockEntry = $getFirstBlockByBlockEntry;
    }

    /**
     * @param BlockEntryInterface $blockEntry
     * @return bool
     * @throws NoSuchEntityException
     */
    public function execute(BlockEntryInterface $blockEntry): bool
    {
        $page = $this->getFirstBlockByBlockEntry->execute($blockEntry);
        return $page === null || $blockEntry->isMaintained();
    }
}
