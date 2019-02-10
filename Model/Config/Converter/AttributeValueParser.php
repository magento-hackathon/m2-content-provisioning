<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMNode;

class AttributeValueParser
{
    /**
     * @param DOMNode $node
     * @param string $attributeName
     * @param null|string $defaultValue
     * @return null|string
     */
    public function execute(DOMNode $node, string $attributeName, ?string $defaultValue = null): ?string
    {
        $item = $node->attributes->getNamedItem($attributeName);
        return $item ? $item->nodeValue : $defaultValue;
    }
}
