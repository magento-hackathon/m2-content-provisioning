<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

interface ContentInstallerInterface
{
    /**
     * Apply all configured CMS content changes
     *
     * @return void
     */
    public function install(): void;
}
