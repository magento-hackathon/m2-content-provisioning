<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

interface StrategyInterface
{
    /**
     * @param string|null $moduleName
     * @return string
     */
    public function buildTargetPath(?string $moduleName = null): string;
}
