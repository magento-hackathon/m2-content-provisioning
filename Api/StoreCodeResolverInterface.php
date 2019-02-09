<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

interface StoreCodeResolverInterface
{
    /**
     * @param string $code
     * @return string[]
     */
    public function execute(string $code): array;
}
