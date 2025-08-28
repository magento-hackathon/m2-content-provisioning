<?php
declare(strict_types=1);

/**
 * Copyright (c) 2025 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 *
 * @copyright  Copyright (c) 2025 TechDivision GmbH (https://www.techdivision.com)
 * @author     TechDivision Team Zero <zero@techdivision.com>
 * @link       https://www.techdivision.com/
 */

namespace Firegento\ContentProvisioning\Model\DTO;

class CmsBlockDto
{
    /**
     * @var string
     */
    private string $identifier;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $key;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string[]
     */
    private array $storeCodes;

    /**
     * @var bool
     */
    private bool $isActive;

    /**
     * @var bool
     */
    private bool $isMaintained;

    /**
     * @param string $identifier
     * @param string $title
     * @param string $key
     * @param string $content
     * @param string[] $storeCodes
     * @param bool $isActive
     * @param bool $isMaintained
     */
    public function __construct(
        string $identifier = '',
        string $title = '',
        string $key = '',
        string $content = '',
        array $storeCodes = [],
        bool $isActive = false,
        bool $isMaintained = false
    ) {
        $this->identifier = $identifier;
        $this->title = $title;
        $this->key = $key;
        $this->content = $content;
        $this->storeCodes = $storeCodes;
        $this->isActive = $isActive;
        $this->isMaintained = $isMaintained;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getStoreCodes(): array
    {
        return $this->storeCodes;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function isMaintained(): bool
    {
        return $this->isMaintained;
    }

    /**
     * @param string $identifier
     * @return self
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string[] $storeCodes
     * @return self
     */
    public function setStoreCodes(array $storeCodes): self
    {
        $this->storeCodes = $storeCodes;
        return $this;
    }

    /**
     * @param bool $isActive
     * @return self
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @param bool $isMaintained
     * @return self
     */
    public function setIsMaintained(bool $isMaintained): self
    {
        $this->isMaintained = $isMaintained;
        return $this;
    }
}
