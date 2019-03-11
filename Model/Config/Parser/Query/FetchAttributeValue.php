<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser\Query;

use DOMNode;

class FetchAttributeValue
{
    /**
     * @param DOMNode $node
     * @param string $attributeName
     * @param null|string $defaultValue
     * @return string
     */
    public function execute(DOMNode $node, string $attributeName, string $defaultValue = '')
    {
        $item = $node->attributes->getNamedItem($attributeName);
        return $item ? $item->nodeValue : $defaultValue;
    }
}
