<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Strategy;

use Firegento\ContentProvisioning\Api\StrategyInterface;

class ExportToModule implements StrategyInterface
{
    /**
     * @param string|null $moduleName
     * @return string
     */
    public function buildTargetPath(?string $moduleName = null): string
    {
        // TODO: Implement buildTargetPath() method.
    }
}
