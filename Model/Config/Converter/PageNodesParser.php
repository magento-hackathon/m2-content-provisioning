<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

use DOMDocument;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Magento\Framework\Exception\LocalizedException;

class PageNodesParser
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
     * @var ContentElementKeyBuilder
     */
    private $contentElementKeyBuilder;

    /**
     * @var ContentHeadingParser
     */
    private $contentHeadingParser;

    /**
     * @var SeoNodeParser
     */
    private $seoNodeParser;

    /**
     * @var DesignNodeParser
     */
    private $designNodeParser;

    /**
     * @var CustomDesignNodeParser
     */
    private $customDesignNodeParser;

    /**
     * @var TitleNodeParser
     */
    private $titleNodeParser;

    /**
     * @param AttributeValueParser $attributeValueParser
     * @param StoresNodeParser $storesNodeParser
     * @param ContentNodeParser $contentNodeParser
     * @param ContentElementKeyBuilder $contentElementKeyBuilder
     * @param ContentHeadingParser $contentHeadingParser
     * @param SeoNodeParser $seoNodeParser
     * @param DesignNodeParser $designNodeParser
     * @param CustomDesignNodeParser $customDesignNodeParser
     * @param TitleNodeParser $titleNodeParser
     */
    public function __construct(
        AttributeValueParser $attributeValueParser,
        StoresNodeParser $storesNodeParser,
        ContentNodeParser $contentNodeParser,
        ContentElementKeyBuilder $contentElementKeyBuilder,
        ContentHeadingParser $contentHeadingParser,
        SeoNodeParser $seoNodeParser,
        DesignNodeParser $designNodeParser,
        CustomDesignNodeParser $customDesignNodeParser,
        TitleNodeParser $titleNodeParser
    ) {
        $this->attributeValueParser = $attributeValueParser;
        $this->storesNodeParser = $storesNodeParser;
        $this->contentNodeParser = $contentNodeParser;
        $this->contentElementKeyBuilder = $contentElementKeyBuilder;
        $this->contentHeadingParser = $contentHeadingParser;
        $this->seoNodeParser = $seoNodeParser;
        $this->designNodeParser = $designNodeParser;
        $this->customDesignNodeParser = $customDesignNodeParser;
        $this->titleNodeParser = $titleNodeParser;
    }

    /**
     * @param DOMDocument $document
     * @return array
     * @throws LocalizedException
     */
    public function execute(DOMDocument $document): array
    {
        $output = [];
        foreach ($document->getElementsByTagName('page') as $node) {
            $identifier = $this->attributeValueParser->execute($node, 'identifier');
            $stores = $this->storesNodeParser->execute($node);
            $key = $this->contentElementKeyBuilder->build($identifier, $stores, 'page');
            $output[$key] = array_merge(
                [
                    PageEntryInterface::KEY => $key,
                    PageEntryInterface::IDENTIFIER => $identifier,
                    PageEntryInterface::TITLE => $this->titleNodeParser->execute($node),
                    PageEntryInterface::IS_ACTIVE => $this->castBoolean(
                        $this->attributeValueParser->execute($node, 'active', null)
                    ),
                    PageEntryInterface::IS_MAINTAINED => $this->castBoolean(
                        (bool)$this->attributeValueParser->execute($node, 'maintained', null)
                    ),
                    PageEntryInterface::STORES => $stores,
                    PageEntryInterface::CONTENT => $this->contentNodeParser->execute($node),
                    PageEntryInterface::CONTENT_HEADING => $this->contentHeadingParser->execute($node),
                ],
                $this->seoNodeParser->execute($node),
                $this->designNodeParser->execute($node),
                $this->customDesignNodeParser->execute($node)
            );
        }
        return $output;
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
