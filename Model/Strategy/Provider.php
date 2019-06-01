<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Strategy;

use Firegento\ContentProvisioning\Api\StrategyInterface;
use Firegento\ContentProvisioning\Api\StrategyProviderInterface;
use Magento\Framework\Exception\NotFoundException;

class Provider implements StrategyProviderInterface
{
    /**
     * @var array|StrategyInterface[]
     */
    private $strategies;

    /**
     * Provider constructor.
     * @param StrategyInterface[] $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @param string $strategyCode
     * @return StrategyInterface
     *
     * @throws NotFoundException
     */
    public function get(string $strategyCode): StrategyInterface
    {
        $strategy = $this->strategies[$strategyCode] ?? null;

        if (!$strategy) {
            throw new NotFoundException(__('Strategy %s not found.', $strategyCode));
        }

        return $strategy;
    }
}
