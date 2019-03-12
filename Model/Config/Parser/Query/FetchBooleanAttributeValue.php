<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser\Query;

use DOMNode;

class FetchBooleanAttributeValue
{
    /**
     * @var FetchAttributeValue
     */
    private $fetchAttributeValue;

    /**
     * @param FetchAttributeValue $fetchAttributeValue
     */
    public function __construct(
        FetchAttributeValue $fetchAttributeValue
    ) {
        $this->fetchAttributeValue = $fetchAttributeValue;
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
            (string)$this->fetchAttributeValue->execute($node, $attributeName, (string)$defaultValue)
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
