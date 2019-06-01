<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterfaceFactory;
use Magento\Framework\Exception\NotFoundException;

class GetPageEntryByKey
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

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
     * @param string $key
     *
     * @return PageEntryInterface
     * @throws NotFoundException
     */
    public function get(string $key)
    {
        $pages = $this->configuration->getList()['pages'] ?? [];

        if (!array_key_exists($key, $pages)) {
            throw new NotFoundException(__('Page with key %1 not found.', $key));
        }

        $item = $this->pageEntryFactory->create(['data' => $pages[$key]]);

        return $item;
    }
}
