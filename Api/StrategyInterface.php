<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

interface StrategyInterface
{
    /**
     * @return string
     */
    public function getXmlPath(): string;

    /**
     * @return string
     */
    public function getContentDirectoryPath(): string;

    /**
     * @return string
     */
    public function getMediaDirectoryPath(): string;

    /**
     * @return string
     */
    public function getContentNamespacePath(): string;

    /**
     * @return string
     */
    public function getMediaNamespacePath(): string;
}
