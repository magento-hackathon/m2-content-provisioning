<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;

class TitleNodeParser
{
    /**
     * @var ChildNodeValueParser
     */
    private $childNodeValueParser;

    /**
     * @param ChildNodeValueParser $childNodeValueParser
     */
    public function __construct(
        ChildNodeValueParser $childNodeValueParser
    ) {
        $this->childNodeValueParser = $childNodeValueParser;
    }

    /**
     * @param DOMElement $element
     * @return string
     */
    public function execute(DOMElement $element): string
    {
        return (string)$this->childNodeValueParser->execute($element, 'title');
    }
}
