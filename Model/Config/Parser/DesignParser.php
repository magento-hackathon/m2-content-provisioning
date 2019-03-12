<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchChildNodeValue;

class DesignParser implements ConfigParserInterface
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
        $nodes = $element->getElementsByTagName('design');
        if ($nodes->length > 0) {
            $node = $nodes->item(0);
            return [
                PageEntryInterface::PAGE_LAYOUT => $this->fetchChildNodeValue->execute($node, 'layout'),
                PageEntryInterface::LAYOUT_UPDATE_XML => $this->fetchChildNodeValue->execute($node, 'layout_xml'),
            ];
        }

        return [];
    }
}
