<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchAttributeValue;
use Firegento\ContentProvisioning\Model\Resolver\ContentResolverProvider;
use Magento\Framework\Exception\LocalizedException;

class ContentParser implements ConfigParserInterface
{
    /**
     * @var ContentResolverProvider
     */
    private $contentResolverProvider;

    /**
     * @var FetchAttributeValue
     */
    private $fetchAttributeValue;

    /**
     * @var string
     */
    private $arrayKey;

    /**
     * @param ContentResolverProvider $contentResolverProvider
     * @param FetchAttributeValue $fetchAttributeValue
     * @param string $arrayKey
     */
    public function __construct(
        ContentResolverProvider $contentResolverProvider,
        FetchAttributeValue $fetchAttributeValue,
        string $arrayKey = PageEntryInterface::CONTENT
    ) {
        $this->contentResolverProvider = $contentResolverProvider;
        $this->fetchAttributeValue = $fetchAttributeValue;
        $this->arrayKey = $arrayKey;
    }

    /**
     * @param DOMElement $element
     * @return array
     * @throws LocalizedException
     */
    public function execute(DOMElement $element): array
    {
        $contentNode = $element->getElementsByTagName('content')->item(0);
        $type = $this->fetchAttributeValue->execute($contentNode, 'type', 'plain');
        $contentResolver = $this->contentResolverProvider->get($type);

        return [
            $this->arrayKey => $contentResolver->execute($contentNode)
        ];
    }
}
