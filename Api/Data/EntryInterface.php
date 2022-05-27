<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api\Data;

/**
 * @api
 */
interface EntryInterface
{
    public const KEY = 'key';
    public const IS_MAINTAINED = 'is_maintained';
    public const STORES = 'stores';
    public const MEDIA_DIRECTORY = 'media_directory';
    public const MEDIA_FILES = 'media_files';

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return bool
     */
    public function isMaintained();

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained($isMaintained);

    /**
     * @return array
     */
    public function getStores();

    /**
     * @param array $stores
     */
    public function setStores(array $stores);

    /**
     * @return string
     */
    public function getMediaDirectory();

    /**
     * @param string $path
     */
    public function setMediaDirectory($path);

    /**
     * @return array
     */
    public function getMediaFiles();

    /**
     * @param array $files
     */
    public function setMediaFiles(array $files);
}
