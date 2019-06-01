<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;

interface ExportInterface
{
    /**
     * @param StrategyInterface $strategy
     * @param EntryInterface $entry
     * @return void
     */
    public function execute(StrategyInterface $strategy, EntryInterface $entry): void;
}