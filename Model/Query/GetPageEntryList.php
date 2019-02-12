<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterfaceFactory;

class GetPageEntryList
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var PageEntryInterface[]
     */
    private $items = [];

    /**
     * @var PageEntryInterfaceFactory
     */
    private $pageEntryFactory;

    /**
     * @param ConfigurationInterface $configuration
     * @param PageEntryInterfaceFactory $pageEntryFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        PageEntryInterfaceFactory $pageEntryFactory
    ) {
        $this->configuration = $configuration;
        $this->pageEntryFactory = $pageEntryFactory;
    }

    /**
     * Prepare items array by transforming all configured entries into content entry data models
     */
    private function prepare(): void
    {
        if (empty($this->items)) {
            $pages = $this->configuration->getList()['pages'] ?? [];
            foreach ($pages as $data) {
                $item = $this->pageEntryFactory->create(['data' => $data]);
                $this->items[] = $item;
            }
        }
    }

    /**
     * @return PageEntryInterface[]
     */
    public function get(): array
    {
        $this->prepare();
        return $this->items;
    }
}