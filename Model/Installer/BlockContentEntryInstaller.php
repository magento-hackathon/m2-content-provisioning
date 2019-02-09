<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Installer;

use Firegento\ContentProvisioning\Api\ContentEntryInstallerInterface;
use Firegento\ContentProvisioning\Api\Data\ContentEntryInterface;

class BlockContentEntryInstaller implements ContentEntryInstallerInterface
{
    /**
     * @param ContentEntryInterface $contentEntry
     */
    public function install(ContentEntryInterface $contentEntry): void
    {
        // TODO: Implement apply() method.
    }
}