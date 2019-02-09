<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

use Firegento\ContentProvisioning\Api\Data\ContentEntryInterface;

interface ContentEntryInstallerInterface
{
    /**
     * @param ContentEntryInterface $contentEntry
     */
    public function install(ContentEntryInterface $contentEntry): void;
}
