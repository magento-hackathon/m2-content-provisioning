<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\Query\GetNodeByKey;
use SimpleXMLElement;

class SeoGenerator implements GeneratorInterface
{
    /**
     * @var GetNodeByKey
     */
    private $getNodeByKey;

    /**
     * @param GetNodeByKey $getNodeByKey
     */
    public function __construct(
        GetNodeByKey $getNodeByKey
    ) {
        $this->getNodeByKey = $getNodeByKey;
    }

    /**
     * @param EntryInterface|PageEntryInterface|BlockEntryInterface $entry
     * @param SimpleXMLElement $xml
     */
    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        $entryNode = $this->getNodeByKey->execute($xml, $entry->getKey());
        if (!$entryNode) {
            return;
        }

        $seoTitle       = $entry->getMetaTitle();
        $seoKeywords    = $entry->getMetaKeywords();
        $seoDescription = $entry->getMetaDescription();

        if (!$seoTitle && !$seoKeywords && !$seoDescription) {
            return;
        }

        $nodeCustomDesign = $entryNode->addChild('seo');

        if ($seoTitle) {
            $nodeCustomDesign->addChild('title', $seoTitle);
        }
        if ($seoKeywords) {
            $nodeCustomDesign->addChild('keywords', $seoKeywords);
        }
        if ($seoDescription) {
            $nodeCustomDesign->addChild('description', $seoDescription);
        }
    }
}