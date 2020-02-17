<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Strategy;

use Firegento\ContentProvisioning\Api\StrategyInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class ExportToVar implements StrategyInterface
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param string $moduleName
     * @param DirectoryList $directoryList
     */
    public function __construct(
        string $moduleName,
        DirectoryList $directoryList
    ) {
        $this->moduleName = $moduleName;
        $this->directoryList = $directoryList;
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getXmlPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->directoryList->getPath(DirectoryList::VAR_DIR),
            'content_provisioning',
            $this->moduleName,
            'content_provisioning.xml'
        ]);
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getContentDirectoryPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->directoryList->getPath(DirectoryList::VAR_DIR),
            'content_provisioning',
            $this->moduleName,
            'content'
        ]);
    }

    /**
     * @return string
     * @throws FileSystemException
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
