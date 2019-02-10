<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;

class SeoNodeParser
{
    /**
     * @var AttributeValueParser
     */
    private $attributeValueParser;
    /**
     * @var ChildNodeValueParser
     */
    private $childNodeValueParser;

    /**
     * @param AttributeValueParser $attributeValueParser
     * @param ChildNodeValueParser $childNodeValueParser
     */
    public function __construct(
        AttributeValueParser $attributeValueParser,
        ChildNodeValueParser $childNodeValueParser
    ) {
        $this->attributeValueParser = $attributeValueParser;
        $this->childNodeValueParser = $childNodeValueParser;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $seoNodes = $element->getElementsByTagName('seo');
        if ($seoNodes->count() > 0) {
            $node = $seoNodes->item(0);
            return [
                PageEntryInterface::META_TITLE => $this->childNodeValueParser->execute($node, 'title'),
                PageEntryInterface::META_KEYWORDS => $this->childNodeValueParser->execute($node, 'keywords'),
                PageEntryInterface::META_DESCRIPTION => $this->childNodeValueParser->execute($node, 'description'),
            ];
        }

        return [];
    }
}
