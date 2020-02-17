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
     * @var ParseConfigurationFile
     */
    private $parseConfigurationFile;

    /**
     * @param Generator              $generateConfig
     * @param ParseConfigurationFile $parseConfigurationFile
     */
    public function __construct(
        Generator              $generateConfig,
        ParseConfigurationFile $parseConfigurationFile
    ) {
        $this->generateConfig         = $generateConfig;
        $this->parseConfigurationFile = $parseConfigurationFile;
    }

    /**
     * @inheritdoc
     */
    public function execute(StrategyInterface $strategy, EntryInterface $entry): void
    {
        if (($entry instanceof BlockEntryInterface) || ($entry instanceof PageEntryInterface)) {
            $entry->setMediaDirectory($strategy->getMediaNamespacePath());
        }

        $xml = $this->parseConfigurationFile->execute($strategy);

        $this->generateConfig->execute($xml, [$entry]);
        $xml->asXML($strategy->getXmlPath());
    }
}