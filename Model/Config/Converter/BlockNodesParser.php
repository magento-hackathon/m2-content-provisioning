<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMDocument;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Magento\Framework\Exception\LocalizedException;

class BlockNodesParser
{
    /**
     * @var AttributeValueParser
     */
    private $attributeValueParser;

    /**
     * @var StoresNodeParser
     */
    private $storesNodeParser;

    /**
     * @var ContentNodeParser
     */
    private $contentNodeParser;

    /**
     * @var TitleNodeParser
     */
    private $titleNodeParser;

    /**
     * @var BooleanAttributeValueParser
     */
    private $booleanAttributeValueParser;

    /**
     * @param AttributeValueParser $attributeValueParser
     * @param BooleanAttributeValueParser $booleanAttributeValueParser
     * @param StoresNodeParser $storesNodeParser
     * @param ContentNodeParser $contentNodeParser
     * @param TitleNodeParser $titleNodeParser
     */
    public function __construct(
        AttributeValueParser $attributeValueParser,
        BooleanAttributeValueParser $booleanAttributeValueParser,
        StoresNodeParser $storesNodeParser,
        ContentNodeParser $contentNodeParser,
        TitleNodeParser $titleNodeParser
    ) {
        $this->attributeValueParser = $attributeValueParser;
        $this->storesNodeParser = $storesNodeParser;
        $this->contentNodeParser = $contentNodeParser;
        $this->titleNodeParser = $titleNodeParser;
        $this->booleanAttributeValueParser = $booleanAttributeValueParser;
    }

    /**
     * @param DOMDocument $document
     * @return array
     * @throws LocalizedException
     */
    public function execute(DOMDocument $document): array
    {
        $output = [];
        foreach ($document->getElementsByTagName('block') as $node) {
            $identifier = $this->attributeValueParser->execute($node, 'identifier');
            $stores = $this->storesNodeParser->execute($node);
            $key = $this->attributeValueParser->execute($node, 'key');
            $output[$key] = [
                BlockEntryInterface::KEY => $key,
                BlockEntryInterface::IDENTIFIER => $identifier,
                BlockEntryInterface::TITLE => $this->titleNodeParser->execute($node),
                BlockEntryInterface::IS_ACTIVE =>
                    $this->booleanAttributeValueParser->execute($node, 'active', 'false'),
                BlockEntryInterface::IS_MAINTAINED =>
                    $this->booleanAttributeValueParser->execute($node, 'maintained', 'false'),
                BlockEntryInterface::STORES => $stores,
                BlockEntryInterface::CONTENT => $this->contentNodeParser->execute($node),
            ];
        }
        return $output;
    }
}
