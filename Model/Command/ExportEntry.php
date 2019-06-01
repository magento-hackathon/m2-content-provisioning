<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\ExportInterface;
use Firegento\ContentProvisioning\Api\StrategyInterface;

class ExportEntry implements ExportInterface
{
    /**
     * @param StrategyInterface $strategy
     * @param EntryInterface $entry
     * @return void
     */
    public function execute(StrategyInterface $strategy, EntryInterface $entry): void
    {
        
    }
}