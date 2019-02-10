<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;

class ContentHeadingParser
{
    /**
     * @var AttributeValueParser
     */
    private $attributeValueParser;

    /**
     * @param AttributeValueParser $attributeValueParser
     */
    public function __construct(
        AttributeValueParser $attributeValueParser
    ) {
        $this->attributeValueParser = $attributeValueParser;
    }

    /**
     * @param DOMElement $element
     * @return string
     */
    public function execute(DOMElement $element): string
    {
        $contentNodes = $element->getElementsByTagName('content');
        $contentNode = $contentNodes[0];
        return (string)$this->attributeValueParser->execute($contentNode, 'heading', '');
    }
}
