<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\ContentInstallerInterface;

class ContentInstaller implements ContentInstallerInterface
{
    /**
     * Apply all configured CMS content changes
     *
     * @return void
     */
    public function install(): void
    {
        // TODO: Implement install() method.
    }
}