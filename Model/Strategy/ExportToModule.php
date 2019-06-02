<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Strategy;

use Firegento\ContentProvisioning\Api\StrategyInterface;
use Magento\Framework\Module\Dir\Reader;

class ExportToModule implements StrategyInterface
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @param string $moduleName
     * @param Reader $moduleReader
     */
    public function __construct(
        string $moduleName,
        Reader $moduleReader
    ) {
        $this->moduleName = $moduleName;
        $this->moduleReader = $moduleReader;
    }

    /**
     * @return string
     */
    public function getXmlPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->moduleReader->getModuleDir('etc', $this->moduleName),
            'content_provisioning.xml'
        ]);
    }

    /**
     * @return string
     */
    public function getContentDirectoryPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->moduleReader->getModuleDir('', $this->moduleName),
            'content'
        ]);
    }

    /**
     * @return string
     */
    public function getMediaDirectoryPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->getContentDirectoryPath(),
            'media'
        ]);
    }

    /**
     * @return string
     */
    public function getContentNamespacePath(): string
    {
        return $this->moduleName . '::content';
    }

    /**
     * @return string
     */
    public function getMediaNamespacePath(): string
    {
        return $this->moduleName . '::content/media';
    }
}
