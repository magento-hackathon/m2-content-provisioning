<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Magento\Framework\Exception\NotFoundException;

class GetBlockEntryByKey
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var BlockEntryInterfaceFactory
     */
    private $blockEntryFactory;

    /**
     * @param ConfigurationInterface $configuration
     * @param BlockEntryInterfaceFactory $blockEntryFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        BlockEntryInterfaceFactory $blockEntryFactory
    ) {
        $this->configuration = $configuration;
        $this->blockEntryFactory = $blockEntryFactory;
    }

    /**
     * @return BlockEntryInterface[]
     * @throws NotFoundException
     */
    public function get(string $key)
    {
        $blocks = $this->configuration->getList()['blocks'] ?? [];

        if (!array_key_exists($key, $blocks)) {
            throw new NotFoundException(__('Block with key %1 not found.', $key));
        }

        $item = $this->blockEntryFactory->create(['data' => $blocks[$key]]);

        return $item;
    }
}
