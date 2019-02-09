<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api\Data;

interface ContentEntryInterface
{
    /**
     * Data entry keys
     */
    const TYPE = 'type';
    const IDENTIFIER = 'identifier';
    const CONTENT = 'content';
    const STORES = 'stores';
    const IS_MAINTAINED = 'is_maintained';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return array
     */
    public function getStores(): array;

    /**
     * @return bool
     */
    public function isMaintained(): bool;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void;

    /**
     * @param string $content
     */
    public function setContent(string $content): void;

    /**
     * @param string[] $stores
     */
    public function setStores(array $stores): void;

    /**
     * @param bool $isMaintained
     */
    public function setIsMaintained(bool $isMaintained): void;
}
