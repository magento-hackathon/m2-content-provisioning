<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;

class CustomDesignNodeParser
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
        $nodes = $element->getElementsByTagName('custom_design');
        if ($nodes->count() > 0) {
            $node = $nodes->item(0);
            return [
                PageEntryInterface::CUSTOM_THEME_FROM => $this->childNodeValueParser->execute($node, 'from'),
                PageEntryInterface::CUSTOM_THEME_TO => $this->childNodeValueParser->execute($node, 'to'),
                PageEntryInterface::CUSTOM_THEME => $this->childNodeValueParser->execute($node, 'theme_id'),
                PageEntryInterface::CUSTOM_ROOT_TEMPLATE => $this->childNodeValueParser->execute($node, 'layout'),
            ];
        }

        return [];
    }
}
