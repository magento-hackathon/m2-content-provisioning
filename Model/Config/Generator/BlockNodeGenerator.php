<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\Cast\BooleanValue;
use Firegento\ContentProvisioning\Model\Config\Generator\Query\GetNodeByKey;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\PageInterface;
use SimpleXMLElement;

class BlockNodeGenerator implements GeneratorInterface
{
    /**
     * @var BooleanValue
     */
    private $castBooleanValue;

    /**
     * @var GetNodeByKey
     */
    private $getNodeByKey;

    /**
     * @param BooleanValue $castBooleanValue
     * @param GetNodeByKey $getNodeByKey
     */
    public function __construct(
        BooleanValue $castBooleanValue,
        GetNodeByKey $getNodeByKey
    ) {
        $this->castBooleanValue = $castBooleanValue;
        $this->getNodeByKey = $getNodeByKey;
    }

    /**
     * @param EntryInterface|PageInterface|BlockInterface $entry
     * @param SimpleXMLElement                            $xml
     */
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        $node = $this->getNodeByKey->execute($xml, $entry->getKey());
        if ($node) {
            $dom = dom_import_simplexml($node);
            $dom->parentNode->removeChild($dom);
        }

        $childNode = $xml->addChild('block');
        $childNode->addAttribute('key', $entry->getKey());
        $childNode->addAttribute('identifier', $entry->getIdentifier());
        $childNode->addAttribute('active', $this->castBooleanValue->execute($entry->isActive()));
        $childNode->addAttribute('maintained', $this->castBooleanValue->execute($entry->isMaintained()));
    }
}