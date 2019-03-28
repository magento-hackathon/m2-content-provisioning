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
    public function getKey()
    {
        return (string)$this->getData(BlockEntryInterface::KEY);
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->setData(BlockEntryInterface::KEY, $key);
    }

    /**
     * @return bool
     */
    public function isMaintained()
    {
        return (bool)$this->getData(BlockEntryInterface::IS_MAINTAINED);
    }

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained($isMaintained)
    {
        $this->setData(BlockEntryInterface::IS_MAINTAINED, $isMaintained);
    }

    /**
     * @return array
     */
    public function getStores()
    {
        return (array)$this->getData(BlockEntryInterface::STORES);
    }

    /**
     * @param array $stores
     */
    public function setStores(array $stores)
    {
        $this->setData(BlockEntryInterface::STORES, $stores);
    }

    /**
     * @return string
     */
    public function getMediaDirectory()
    {
        return (string)$this->getData(BlockEntryInterface::MEDIA_DIRECTORY);
    }

    /**
     * @param string $path
     */
    public function setMediaDirectory($path)
    {
        $this->setData(BlockEntryInterface::MEDIA_DIRECTORY, $path);
    }

    /**
     * @return array
     */
    public function getMediaFiles()
    {
        return (array)$this->getData(BlockEntryInterface::MEDIA_FILES);
    }

    /**
     * @param array $files
     */
    public function setMediaFiles(array $files)
    {
        $this->setData(BlockEntryInterface::MEDIA_FILES, $files);
    }
}
