<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

/**
 * @api
 */
interface TargetMediaDirectoryPathProviderInterface
{
    /**
     * @return string
     */
    public function get(): string;
}
