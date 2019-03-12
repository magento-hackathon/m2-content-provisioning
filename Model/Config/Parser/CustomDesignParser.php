<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchChildNodeValue;

class CustomDesignParser implements ConfigParserInterface
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
        $nodes = $element->getElementsByTagName('custom_design');
        if ($nodes->length > 0) {
            $node = $nodes->item(0);
            return [
                PageEntryInterface::CUSTOM_THEME_FROM => $this->fetchChildNodeValue->execute($node, 'from'),
                PageEntryInterface::CUSTOM_THEME_TO => $this->fetchChildNodeValue->execute($node, 'to'),
                PageEntryInterface::CUSTOM_THEME => $this->fetchChildNodeValue->execute($node, 'theme_id'),
                PageEntryInterface::CUSTOM_ROOT_TEMPLATE => $this->fetchChildNodeValue->execute($node, 'layout'),
            ];
        }

        return [];
    }
}
