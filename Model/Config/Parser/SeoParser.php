<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchChildNodeValue;

class SeoParser implements ConfigParserInterface
{
    /**
     * @var FetchChildNodeValue
     */
    private $fetchChildNodeValue;

    /**
     * @param FetchChildNodeValue $fetchChildNodeValue
     */
    public function __construct(
        FetchChildNodeValue $fetchChildNodeValue
    ) {
        $this->fetchChildNodeValue = $fetchChildNodeValue;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $seoNodes = $element->getElementsByTagName('seo');
        if ($seoNodes->length > 0) {
            $node = $seoNodes->item(0);
            return [
                PageEntryInterface::META_TITLE => $this->fetchChildNodeValue->execute($node, 'title'),
                PageEntryInterface::META_KEYWORDS => $this->fetchChildNodeValue->execute($node, 'keywords'),
                PageEntryInterface::META_DESCRIPTION => $this->fetchChildNodeValue->execute($node, 'description'),
            ];
        }

        return [];
    }
}
