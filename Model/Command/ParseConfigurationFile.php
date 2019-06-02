<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\StrategyInterface;
use SimpleXMLElement;

class ParseConfigurationFile
{
    public function execute(StrategyInterface $strategy): SimpleXMLElement
    {
        if (!\file_exists($strategy->getXmlPath())) {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><config xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"urn:magento:module:Firegento/ContentProvisioning/etc/content_provisioning.xsd\"></config>");;
            $xml->saveXML($strategy->getXmlPath());
        }

        return new SimpleXMLElement($strategy->getXmlPath(), 0, true);
    }
}