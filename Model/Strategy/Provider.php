<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Strategy;

use Firegento\ContentProvisioning\Api\StrategyInterface;
use Firegento\ContentProvisioning\Api\StrategyProviderInterface;
use Magento\Framework\Exception\InputException;
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
     * @throws InputException
     */
    public function __construct(array $strategies)
    {
        foreach ($strategies as $strategy) {
            if (!($strategy instanceof StrategyInterface)) {
                throw new InputException(__(
                    'Strategy must be instance of %interface',
                    ['interface' => StrategyInterface::class]
                ));
            }
        }
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
            throw new NotFoundException(__('Strategy %strategy_code not found.', ['strategy_code' => $strategyCode]));
        }

        return $strategy;
    }
}
