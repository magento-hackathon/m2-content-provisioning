<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

use DOMElement;

/**
 * @api
 */
interface ContentResolverInterface
{
    /**
     * @param DOMElement $node
     * @return string
     */
    public function execute(DOMElement $node): string;
}
