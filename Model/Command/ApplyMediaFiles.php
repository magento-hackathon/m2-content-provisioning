<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\TargetMediaDirectoryPathProviderInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverInterface;

class ApplyMediaFiles
{
    /**
     * @var TargetMediaDirectoryPathProviderInterface
     */
    private $targetMediaDirectoryPathProvider;

    /**
     * @var DriverInterface
     */
    private $fileSystemDriver;

    /**
     * @param TargetMediaDirectoryPathProviderInterface $targetMediaDirectoryPathProvider
     * @param DriverInterface $fileSystemDriver
     */
    public function __construct(
        TargetMediaDirectoryPathProviderInterface $targetMediaDirectoryPathProvider,
        DriverInterface $fileSystemDriver
    ) {
        $this->targetMediaDirectoryPathProvider = $targetMediaDirectoryPathProvider;
        $this->fileSystemDriver = $fileSystemDriver;
    }

    /**
     * @param EntryInterface $entry
     * @throws FileSystemException
     */
    public function execute(EntryInterface $entry): void
    {
        if ($entry->getMediaDirectory() === null) {
            return;
        }

        $sourceDirPath = $entry->getMediaDirectory();
        foreach ($entry->getMediaFiles() as $fileName) {
            $this->copyFile($sourceDirPath, $fileName);
        }
    }

    /**
     * @param string $sourceDirPath
     * @param string $fileName
     * @throws FileSystemException
     */
    private function copyFile(string $sourceDirPath, string $fileName): void
    {
        $targetDirPath = $this->targetMediaDirectoryPathProvider->get();
        $sourcePathname = $sourceDirPath . DIRECTORY_SEPARATOR . $fileName;
        $targetPathname = $targetDirPath . DIRECTORY_SEPARATOR . $fileName;

        if ($this->fileSystemDriver->isFile($sourcePathname)
            && $this->fileSystemDriver->isReadable($sourcePathname)
        ) {
            $this->createDirectory($targetDirPath, $sourceDirPath, $sourcePathname);
            $this->fileSystemDriver->copy($sourcePathname, $targetPathname);
        }
    }

    /**
     * @param string $targetDirPath
     * @param string $sourceDirPath
     * @param string $sourcePathname
     * @throws FileSystemException
     */
    private function createDirectory(string $targetDirPath, string $sourceDirPath, string $sourcePathname): void
    {
        $subPath = str_replace(
            $sourceDirPath,
            '',
            $this->fileSystemDriver->getParentDirectory($sourcePathname)
        );
        $pathname = $targetDirPath . DIRECTORY_SEPARATOR . $subPath;
        if (!$this->fileSystemDriver->isDirectory($pathname)) {
            $this->fileSystemDriver->createDirectory($pathname);
        }
    }
}
