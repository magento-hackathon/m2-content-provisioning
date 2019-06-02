<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\GeneratorChain;
use SimpleXMLElement;

class Generator
{
    /**
     * @var GeneratorChain
     */
    private $pageGeneratorChain;
    /**
     * @var GeneratorChain
     */
    private $blockGeneratorChain;

    public function __construct(
        GeneratorChain $blockGeneratorChain,
        GeneratorChain $pageGeneratorChain
    ) {
        $this->blockGeneratorChain = $blockGeneratorChain;
        $this->pageGeneratorChain  = $pageGeneratorChain;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param EntryInterface[] $entries
     *
     * @return void
     */
    public function execute(SimpleXMLElement $xml, array $entries): void
    {
        foreach ($entries as $entry) {
            if ($entry instanceof PageEntryInterface) {
                $this->pageGeneratorChain->execute($entry, $xml);
            }
            if ($entry instanceof BlockEntryInterface) {
                $this->blockGeneratorChain->execute($entry, $xml);
            }
        }
    }
}