<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

use Magento\Framework\Config\DataInterface;

/**
 * @api
 */
interface ConfigurationInterface extends DataInterface
{
    /**
     * @return array
     */
    public function getList(): array;
}
