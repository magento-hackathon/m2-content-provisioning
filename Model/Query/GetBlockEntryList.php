<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;

class GetBlockEntryList
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var BlockEntryInterface[]
     */
    private $items = [];

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
     * Prepare items array by transforming all configured entries into content entry data models
     */
    private function prepare()
    {
        if (empty($this->items)) {
            $blocks = $this->configuration->getList()['blocks'] ?? [];
            foreach ($blocks as $block) {
                $item = $this->blockEntryFactory->create(['data' => $block]);
                $this->items[] = $item;
            }
        }
    }

    /**
     * @return BlockEntryInterface[]
     */
    public function get()
    {
        $this->prepare();
        return $this->items;
    }
}