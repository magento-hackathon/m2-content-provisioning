<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\BlockInstaller;

class InstallTest extends TestCase
{
    public function testInstall()
    {
        $this->initBlockEntries();

        $this->installer->install();

        // Verify, that block are in database like defined
        $this->compareBlockWithEntryForStore(1);
        $this->compareBlockWithEntryForStore(2);
    }
}
