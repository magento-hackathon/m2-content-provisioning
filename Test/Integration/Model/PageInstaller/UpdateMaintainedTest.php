<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\PageInstaller;

class UpdateMaintainedTest extends TestCase
{
    public function testInstall()
    {
        $this->initEntries();

        $this->installer->install();

        // Change page entry values
        $this->pageEntries[1]->setTitle('Changed Page 1');
        $this->pageEntries[1]->setIsActive(true);
        $this->pageEntries[1]->setContent('New Content');

        $this->pageEntries[2]->setTitle('Changed Page 2');
        $this->pageEntries[2]->setIsActive(true);
        $this->pageEntries[2]->setContent('New Content');

        $this->pageEntries[3]->setTitle('Changed Page 3');
        $this->pageEntries[3]->setContent('New Content 333');
        $this->pageEntries[3]->setMetaDescription('New Meta description');
        $this->pageEntries[3]->setPageLayout('1column');
        $this->pageEntries[3]->setContentHeading('Another Content Heading');

        // Execute installer a second time
        $this->installer->install();

        // Verify that first and third page was updated
        $this->comparePageWithEntryForStore(1);
        $this->comparePageWithEntryForStore(3);

        // Verify that second page did not change
        $entry = $this->pageEntries[2];
        $block = $this->getPageByPageEntry($entry);

        $this->assertNotSame($block->getTitle(), $entry->getTitle());
        $this->assertNotSame($block->getContent(), $entry->getContent());
    }
}
