<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\GeneratorChain;
use SimpleXMLElement;

class Generator
{
    /**
     * @var GeneratorChain
     */
    private $pageGeneratorChain;
    /**
     * @var GeneratorChain
     */
    private $blockGeneratorChain;

    public function __construct(
        GeneratorChain $blockGeneratorChain,
        GeneratorChain $pageGeneratorChain
    ) {
        $this->blockGeneratorChain = $blockGeneratorChain;
        $this->pageGeneratorChain  = $pageGeneratorChain;
    }

    /**
     * @param EntryInterface[] $entries
     * @return string
     */
    public function toXml(array $entries): string
    {
        // TODO: Parse file if exists and create new if not
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><config xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"urn:magento:module:Firegento/ContentProvisioning/etc/content_provisioning.xsd\"></config>");;

        foreach ($entries as $entry) {
            if ($entry instanceof PageEntryInterface) {
                $this->pageGeneratorChain->execute($entry, $xml);
            }
            if ($entry instanceof BlockEntryInterface) {
                $this->blockGeneratorChain->execute($entry, $xml);
            }
        }

        return $xml->asXML();
    }
}