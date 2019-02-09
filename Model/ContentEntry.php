<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\Data\ContentEntryInterface;
use Magento\Framework\DataObject;

class ContentEntry extends DataObject implements ContentEntryInterface
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return (string)$this->getData(ContentEntryInterface::TYPE);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return (string)$this->getData(ContentEntryInterface::KEY);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return (string)$this->getData(ContentEntryInterface::IDENTIFIER);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return (string)$this->getData(ContentEntryInterface::CONTENT);
    }

    /**
     * @return array
     */
    public function getStores(): array
    {
        return (array)$this->getData(ContentEntryInterface::STORES);
    }

    /**
     * @return bool
     */
    public function isMaintained(): bool
    {
        return (bool)$this->getData(ContentEntryInterface::IS_MAINTAINED);
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->setData(ContentEntryInterface::TYPE, $type);
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->setData(ContentEntryInterface::KEY, $key);
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->setData(ContentEntryInterface::IDENTIFIER, $identifier);
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->setData(ContentEntryInterface::CONTENT, $content);
    }

    /**
     * @param string[] $stores
     */
    public function setStores(array $stores): void
    {
        $this->setData(ContentEntryInterface::STORES, $stores);
    }

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained(bool $isMaintained): void
    {
        $this->setData(ContentEntryInterface::IS_MAINTAINED, $isMaintained);
    }
}