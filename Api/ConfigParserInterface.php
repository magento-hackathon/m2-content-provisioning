<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

use DOMElement;

interface ConfigParserInterface
{
    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array;
}
