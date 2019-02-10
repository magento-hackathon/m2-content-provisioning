<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMElement;
use Firegento\ContentProvisioning\Model\Resolver\ContentResolverProvider;
use Magento\Framework\Exception\LocalizedException;

class ContentNodeParser
{
    /**
     * @var AttributeValueParser
     */
    private $attributeValueParser;

    /**
     * @var ContentResolverProvider
     */
    private $contentResolverProvider;

    /**
     * @param ContentResolverProvider $contentResolverProvider
     * @param AttributeValueParser $attributeValueParser
     */
    public function __construct(
        ContentResolverProvider $contentResolverProvider,
        AttributeValueParser $attributeValueParser
    ) {
        $this->attributeValueParser = $attributeValueParser;
        $this->contentResolverProvider = $contentResolverProvider;
    }

    /**
     * @param DOMElement $element
     * @return string
     * @throws LocalizedException
     */
    public function execute(DOMElement $element): string
    {
        $contentNode = $element->getElementsByTagName('content')->item(0);
        $type = $this->attributeValueParser->execute($contentNode, 'type', 'plain');
        $contentResolver = $this->contentResolverProvider->get($type);
        return $contentResolver->execute($contentNode);
    }
}
