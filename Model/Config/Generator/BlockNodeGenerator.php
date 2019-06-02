<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\Cast\BooleanValue;
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
        $childNode = $xml->addChild('block');
        $childNode->addAttribute('key', $entry->getKey());
        $childNode->addAttribute('identifier', $entry->getIdentifier());
        $childNode->addAttribute('active', $this->castBooleanValue->execute($entry->isActive()));
    }
}