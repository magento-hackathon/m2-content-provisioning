<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMNode;

class BooleanAttributeValueParser
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
     * @param DOMNode $node
     * @param string $attributeName
     * @param null|string $defaultValue
     * @return bool
     */
    public function execute(DOMNode $node, string $attributeName, ?string $defaultValue = null): bool
    {
        return $this->castBoolean(
            (string)$this->attributeValueParser->execute($node, $attributeName, (string)$defaultValue)
        );
    }

    /**
     * @param string $value
     * @return bool
     */
    private function castBoolean(string $value): bool
    {
        if (in_array($value, ['false', 'no', '0'])) {
            return false;
        }
        return (bool)$value;
    }
}
