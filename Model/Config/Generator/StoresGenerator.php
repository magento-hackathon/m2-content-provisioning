<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use SimpleXMLElement;

class StoresGenerator implements GeneratorInterface
{
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        $node = $xml->xpath('@key='.$entry->getKey());

    }
}