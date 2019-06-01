<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Strategy;

use Firegento\ContentProvisioning\Api\StrategyInterface;

class ExportToModule implements StrategyInterface
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @param string $moduleName
     */
    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getModulePath(): string
    {
        // TODO: Implement getModulePath() method.
    }

    /**
     * @return string
     */
    public function getXmlPath(): string
    {
        // TODO: Implement getXmlPath() method.
    }

    /**
     * @return string
     */
    public function getContentDirectoryPath(): string
    {
        // TODO: Implement getContentDirectoryPath() method.
    }

    /**
     * @return string
     */
    public function getMediaDirectoryPath(): string
    {
        // TODO: Implement getMediaDirectoryPath() method.
    }
}
