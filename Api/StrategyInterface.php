<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

interface StrategyInterface
{
    /**
     * @return string
     */
    public function getTargetPath(): string;
}