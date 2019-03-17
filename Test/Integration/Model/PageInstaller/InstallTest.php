<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\PageInstaller;

class InstallTest extends TestCase
{
    public function testInstall()
    {
        $this->initEntries();

        $this->installer->install();

        // Verify, that pages are in database like defined
        $this->comparePageWithEntryForStore(1);
        $this->comparePageWithEntryForStore(2);
        $this->comparePageWithEntryForStore(3);
    }
}
