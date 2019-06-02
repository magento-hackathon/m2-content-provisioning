<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use SimpleXMLElement;

class ContentGenerator implements GeneratorInterface
{
    /**
     * @param EntryInterface|PageEntryInterface|BlockEntryInterface $entry
     * @param SimpleXMLElement $xml
     */
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        /** @var SimpleXMLElement $node */
        $nodes = $xml->xpath("(page|block)[@key='" . $entry->getKey() . "']");
        if (!$nodes) {
            return;
        }

        $entryNode = array_shift($nodes);
        $node = $entryNode->addChild('content', $entry->getContent());
        if ($entry instanceof PageEntryInterface) {
            $node->addAttribute('heading', $entry->getContentHeading());
        }
    }
}