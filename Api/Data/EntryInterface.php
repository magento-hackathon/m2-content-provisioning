<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api\Data;

interface EntryInterface
{
    const KEY = 'key';
    const IS_MAINTAINED = 'is_maintained';
    const STORES = 'stores';
    const MEDIA_DIRECTORY = 'media_directory';
    const MEDIA_FILES = 'media_files';

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $key
     */
    public function setKey(string $key): void;

    /**
     * @return bool
     */
    public function isMaintained(): bool;

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained(bool $isMaintained): void;

    /**
     * @return array
     */
    public function getStores(): array;

    /**
     * @param array $stores
     */
    public function setStores(array $stores): void;

    /**
     * @return string
     */
    public function getMediaDirectory(): string;

    /**
     * @param string $path
     */
    public function setMediaDirectory(string $path): void;

    /**
     * @return array
     */
    public function getMediaFiles(): array;

    /**
     * @param array $files
     */
    public function setMediaFiles(array $files): void;
}
