<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;
use Firegento\ContentProvisioning\Api\StoreCodeResolverInterface;

class StoresNodeParser
{
    /**
     * @var StoreCodeResolverInterface
     */
    private $storeCodeResolver;

    /**
     * @var AttributeValueParser
     */
    private $attributeValueParser;

    /**
     * @param StoreCodeResolverInterface $storeCodeResolver
     * @param AttributeValueParser $attributeValueParser
     */
    public function __construct(
        StoreCodeResolverInterface $storeCodeResolver,
        AttributeValueParser $attributeValueParser
    ) {
        $this->storeCodeResolver = $storeCodeResolver;
        $this->attributeValueParser = $attributeValueParser;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $output = [];
        foreach ($element->getElementsByTagName('store') as $store) {
            $storeCodes = $this->storeCodeResolver->execute(
                (string)$this->attributeValueParser->execute($store, 'code', '*')
            );
            $output = array_merge($output, $storeCodes);
        }

        if (empty($output)) {
            $output = $this->storeCodeResolver->execute('*');
        }

        return $output;
    }
}
