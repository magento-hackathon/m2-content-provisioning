<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Magento\Cms\Model\Block;

class BlockEntry extends Block implements BlockEntryInterface
{
    /**
     * @return string
     */
    public function getKey(): string
    {
        return (string)$this->getData(BlockEntryInterface::KEY);
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->setData(BlockEntryInterface::KEY, $key);
    }

    /**
     * @return bool
     */
    public function isMaintained(): bool
    {
        return (bool)$this->getData(BlockEntryInterface::IS_MAINTAINED);
    }

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained(bool $isMaintained): void
    {
        $this->setData(BlockEntryInterface::IS_MAINTAINED, $isMaintained);
    }

    /**
     * @return array
     */
    public function getStores(): array
    {
        return (array)$this->getData(BlockEntryInterface::STORES);
    }

    /**
     * @param array $stores
     */
    public function setStores(array $stores): void
    {
        $this->setData(BlockEntryInterface::STORES, $stores);
    }
}
