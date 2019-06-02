<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\Cast\BooleanValue;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\PageInterface;
use SimpleXMLElement;

class PageNodeGenerator implements GeneratorInterface
{
    /**
     * @var BooleanValue
     */
    private $castBooleanValue;

    /**
     * @param BooleanValue $castBooleanValue
     */
    public function __construct(
        BooleanValue $castBooleanValue
    ) {
        $this->castBooleanValue = $castBooleanValue;
    }

    /**
     * @param EntryInterface|PageInterface|BlockInterface $entry
     * @param SimpleXMLElement                            $xml
     */
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        /** @var SimpleXMLElement $node */
        $nodes = $xml->xpath("page[@key='" . $entry->getKey() . "']");
        if ($nodes) {
            $node = array_shift($nodes);
            $dom = dom_import_simplexml($node);
            $dom->parentNode->removeChild($dom);
        }

        $childNode = $xml->addChild('page');
        $childNode->addAttribute('key', $entry->getKey());
        $childNode->addAttribute('identifier', $entry->getIdentifier());
        $childNode->addAttribute('active', $this->castBooleanValue->execute($entry->isActive()));
        $childNode->addAttribute('maintained', $this->castBooleanValue->execute($entry->isMaintained()));
    }
}