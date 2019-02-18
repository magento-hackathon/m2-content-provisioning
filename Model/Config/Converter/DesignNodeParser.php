<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;

class DesignNodeParser
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
        $nodes = $element->getElementsByTagName('design');
        if ($nodes->length > 0) {
            $node = $nodes->item(0);
            return [
                PageEntryInterface::PAGE_LAYOUT => $this->childNodeValueParser->execute($node, 'layout'),
                PageEntryInterface::LAYOUT_UPDATE_XML => $this->childNodeValueParser->execute($node, 'layout_xml'),
            ];
        }

        return [];
    }
}
