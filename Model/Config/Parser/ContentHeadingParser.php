<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchAttributeValue;

class ContentHeadingParser implements ConfigParserInterface
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
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $contentNodes = $element->getElementsByTagName('content');
        $contentNode = $contentNodes[0];
        return [
            PageEntryInterface::CONTENT_HEADING =>
                $this->fetchAttributeValue->execute($contentNode, 'heading', ''),
        ];
    }
}
