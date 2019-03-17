<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Magento\Cms\Model\Page;

class PageEntry extends Page implements PageEntryInterface
{
    /**
     * @return string
     */
    public function getKey(): string
    {
        return (string)$this->getData(PageEntryInterface::KEY);
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->setData(PageEntryInterface::KEY, $key);
    }

    /**
     * @return bool
     */
    public function isMaintained(): bool
    {
        return (bool)$this->getData(PageEntryInterface::IS_MAINTAINED);
    }

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained(bool $isMaintained): void
    {
        $this->setData(PageEntryInterface::IS_MAINTAINED, $isMaintained);
    }

    /**
     * @return array
     */
    public function getStores(): array
    {
        return (array)$this->getData(PageEntryInterface::STORES);
    }

    /**
     * @param array $stores
     */
    public function setStores(array $stores): void
    {
        $this->setData(PageEntryInterface::STORES, $stores);
    }

    /**
     * @return string
     */
    public function getMediaDirectory(): string
    {
        return (string)$this->getData(PageEntryInterface::MEDIA_DIRECTORY);
    }

    /**
     * @param string $path
     */
    public function setMediaDirectory(string $path): void
    {
        $this->setData(PageEntryInterface::MEDIA_DIRECTORY, $path);
    }

    /**
     * @return array
     */
    public function getMediaFiles(): array
    {
        return (array)$this->getData(PageEntryInterface::MEDIA_FILES);
    }

    /**
     * @param array $files
     */
    public function setMediaFiles(array $files): void
    {
        $this->setData(PageEntryInterface::MEDIA_FILES, $files);
    }
}
