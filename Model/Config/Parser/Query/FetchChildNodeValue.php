<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser\Query;

use DOMElement;

class FetchChildNodeValue
{
    /**
     * @param DOMElement $node
     * @param string $childNodeName
     * @return string
     */
    public function execute(DOMElement $node, string $childNodeName): string
    {
        $children = $node->getElementsByTagName($childNodeName);
        if ($children->length > 0) {
            return (string)$children->item(0)->textContent;
        }
        return '';
    }
}
