<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use DOMElement;
use Firegento\ContentProvisioning\Api\ContentResolverInterface;

class PlainContentResolver implements ContentResolverInterface
{
    /**
     * @param DOMElement $node
     * @return string
     */
    public function execute(DOMElement $node): string
    {
        return (string)$node->textContent;
    }
}
