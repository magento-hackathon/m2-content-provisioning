<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchAttributeValue;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchMediaFilesFromContent;
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
     * @var FetchMediaFilesFromContent
     */
    private $fetchMediaFilesFromContent;

    /**
     * @param ContentResolverProvider $contentResolverProvider
     * @param FetchAttributeValue $fetchAttributeValue
     * @param FetchMediaFilesFromContent $fetchMediaFilesFromContent
     * @param string $arrayKey
     */
    public function __construct(
        ContentResolverProvider $contentResolverProvider,
        FetchAttributeValue $fetchAttributeValue,
        FetchMediaFilesFromContent $fetchMediaFilesFromContent,
        string $arrayKey = PageEntryInterface::CONTENT
    ) {
        $this->contentResolverProvider = $contentResolverProvider;
        $this->fetchAttributeValue = $fetchAttributeValue;
        $this->arrayKey = $arrayKey;
        $this->fetchMediaFilesFromContent = $fetchMediaFilesFromContent;
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
        $content = $contentResolver->execute($contentNode);

        return [
            $this->arrayKey => $content,
            EntryInterface::MEDIA_FILES => $this->fetchMediaFilesFromContent->execute($content)
        ];
    }
}
