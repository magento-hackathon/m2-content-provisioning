<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

use Magento\Framework\Exception\NotFoundException;

interface StrategyProviderInterface
{
    /**
     * @param string $strategyCode
     * @return StrategyInterface
     *
     * @throws NotFoundException
     */
    public function get(string $strategyCode): StrategyInterface;
}
