<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Magento\Framework\Model\AbstractModel;

class GenerateConfig
{

    public function toXml(EntryInterface ...$entries): string
    {
        $data = [];

        /** @var EntryInterface|AbstractModel $entry */
        foreach ($entries as $entry) {
            $data = array_merge($data, $entry->getData());
        }

        return (new XmlDOMConstruct('1.0', 'utf-8'))->fromMixed($data)->saveXML();
    }
}