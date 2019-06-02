<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\ExportInterface;
use Firegento\ContentProvisioning\Api\StrategyInterface;
use Firegento\ContentProvisioning\Model\Config\Generator;

class ExportEntry implements ExportInterface
{
    /**
     * @var Generator
     */
    private $generateConfig;

    /**
     * @param Generator $generateConfig
     */
    public function __construct(
        Generator $generateConfig
    ) {
        $this->generateConfig = $generateConfig;
    }

    /**
     * @inheritdoc
     */
    public function execute(StrategyInterface $strategy, EntryInterface $entry): void
    {
        if (($entry instanceof BlockEntryInterface) || ($entry instanceof PageEntryInterface)) {
            $entry->setMediaDirectory($strategy->getMediaNamespacePath());
        }

        $xmlContent = $this->generateConfig->toXml([$entry]);
        file_put_contents($strategy->getXmlPath(), $xmlContent);
    }
}