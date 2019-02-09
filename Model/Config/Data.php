<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;

class Data extends \Magento\Framework\Config\Data implements ConfigurationInterface
{
    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->_data;
    }
}