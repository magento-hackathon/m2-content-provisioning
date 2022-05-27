<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

/**
 * @api
 */
interface StoreCodeResolverInterface
{
    /**
     * @param string $code
     * @return string[]
     */
    public function execute(string $code): array;
}
