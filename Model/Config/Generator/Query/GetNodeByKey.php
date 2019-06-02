<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator\Query;

use SimpleXMLElement;

class GetNodeByKey
{
    public function execute(SimpleXMLElement $xml, string $key): ?SimpleXMLElement
    {
        $nodes = $xml->xpath("(page|block)[@key='" . $key . "']");
        if ($nodes) {
            $node = array_shift($nodes);
        }

        return $node ?? null;
    }
}