<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use DOMElement;
use Firegento\ContentProvisioning\Api\ContentResolverInterface;

class FileContentResolver implements ContentResolverInterface
{
    /**
     * @param DOMElement $node
     * @return string
     */
    public function execute(DOMElement $node): string
    {
        $path = (string)$node->textContent;
        // @TODO: resolve relative and absolute paths
        // @TODO: resolve module namespace prefixes
        return 'foobar -> ' . $path;
    }
}