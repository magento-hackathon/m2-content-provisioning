<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\BlockInstaller;

class UpdateMaintainedTest extends TestCase
{
    public function testInstall()
    {
        $this->initBlockEntries();

        $this->installer->install();

        // Change block entry values
        $this->blockEntries[1]->setTitle('Changed Block 1');
        $this->blockEntries[1]->setIsActive(true);
        $this->blockEntries[1]->setContent('New Content');

        $this->blockEntries[2]->setTitle('Changed Block 2');
        $this->blockEntries[2]->setIsActive(true);
        $this->blockEntries[2]->setContent('New Content');

        // Execute installer a second time
        $this->installer->install();

        // Verify that first block was updated
        $this->compareBlockWithEntryForStore(1);

        // Verify that second block did not change
        $entry = $this->blockEntries[2];
        $block = $this->getBlockByBlockEntry($entry);

        $this->assertNotSame($block->getTitle(), $entry->getTitle());
        $this->assertNotSame($block->getContent(), $entry->getContent());
    }
}
