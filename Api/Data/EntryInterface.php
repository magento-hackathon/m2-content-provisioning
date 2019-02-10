<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api\Data;

interface EntryInterface
{
    const KEY = 'key';
    const IS_MAINTAINED = 'is_maintained';
    const STORES = 'stores';

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
}
