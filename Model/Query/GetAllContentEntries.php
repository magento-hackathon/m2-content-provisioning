<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;
use Firegento\ContentProvisioning\Api\Data\ContentEntryInterface;
use Firegento\ContentProvisioning\Api\Data\ContentEntryInterfaceFactory;

class GetAllContentEntries
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ContentEntryInterfaceFactory
     */
    private $contentEntryFactory;

    /**
     * @var ContentEntryInterface[]
     */
    private $items = [];

    /**
     * @param ConfigurationInterface $configuration
     * @param ContentEntryInterfaceFactory $contentEntryFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ContentEntryInterfaceFactory $contentEntryFactory
    ) {
        $this->configuration = $configuration;
        $this->contentEntryFactory = $contentEntryFactory;
    }

    /**
     * @return ContentEntryInterface[]
     */
    public function get(): array
    {
        if (empty($this->items)) {
            foreach ($this->configuration->getList() as $data) {
                $this->items[] = $this->contentEntryFactory->create(['data' => $data]);
            }
        }
        return $this->items;
    }
}