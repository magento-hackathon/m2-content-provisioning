<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchAttributeValue;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchBooleanAttributeValue;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchChildNodeValue;

class MetaDataParser implements ConfigParserInterface
{
    /**
     * @var FetchAttributeValue
     */
    private $fetchAttributeValue;

    /**
     * @var FetchBooleanAttributeValue
     */
    private $fetchBooleanAttributeValue;

    /**
     * @var FetchChildNodeValue
     */
    private $fetchChildNodeValue;

    /**
     * @param FetchAttributeValue $fetchAttributeValue
     * @param FetchBooleanAttributeValue $fetchBooleanAttributeValue
     * @param FetchChildNodeValue $fetchChildNodeValue
     */
    public function __construct(
        FetchAttributeValue $fetchAttributeValue,
        FetchBooleanAttributeValue $fetchBooleanAttributeValue,
        FetchChildNodeValue $fetchChildNodeValue
    ) {
        $this->fetchAttributeValue = $fetchAttributeValue;
        $this->fetchBooleanAttributeValue = $fetchBooleanAttributeValue;
        $this->fetchChildNodeValue = $fetchChildNodeValue;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        return [
            EntryInterface::KEY => $this->fetchAttributeValue->execute($element, 'key'),
            PageEntryInterface::IDENTIFIER => $this->fetchAttributeValue->execute($element, 'identifier'),
            PageEntryInterface::TITLE => $this->fetchChildNodeValue->execute($element, 'title'),
            PageEntryInterface::IS_ACTIVE =>
                $this->fetchBooleanAttributeValue->execute($element, 'active', 'false'),
            EntryInterface::IS_MAINTAINED =>
                $this->fetchBooleanAttributeValue->execute($element, 'maintained', 'false'),
        ];
    }
}
