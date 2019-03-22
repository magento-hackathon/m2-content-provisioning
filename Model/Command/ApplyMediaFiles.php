<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Api\TargetMediaDirectoryPathProviderInterface;
use SplFileInfo;

class ApplyMediaFiles
{
    /**
     * @var TargetMediaDirectoryPathProviderInterface
     */
    private $targetMediaDirectoryPathProvider;

    /**
     * @param TargetMediaDirectoryPathProviderInterface $targetMediaDirectoryPathProvider
     */
    public function __construct(
        TargetMediaDirectoryPathProviderInterface $targetMediaDirectoryPathProvider
    ) {
        $this->targetMediaDirectoryPathProvider = $targetMediaDirectoryPathProvider;
    }

    /**
     * @param EntryInterface $entry
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
     */
    private function copyFile(string $sourceDirPath, string $fileName): void
    {
        $targetDirPath = $this->targetMediaDirectoryPathProvider->get();
        $sourcePathname = $sourceDirPath . DIRECTORY_SEPARATOR . $fileName;
        $targetPathname = $targetDirPath . DIRECTORY_SEPARATOR . $fileName;

        $sourceInfo = new SplFileInfo($sourcePathname);
        if ($sourceInfo->isFile() && $sourceInfo->isReadable()) {
            $this->createDirectory($targetDirPath, $sourceDirPath, $sourceInfo);
            copy($sourceInfo->getPathname(), $targetPathname);
        }
    }

    /**
     * @param string $targetDirPath
     * @param string $sourceDirPath
     * @param SplFileInfo $sourceInfo
     */
    private function createDirectory(string $targetDirPath, string $sourceDirPath, SplFileInfo $sourceInfo): void
    {
        $subPath = str_replace($sourceDirPath, '', $sourceInfo->getPath());
        $pathname = $targetDirPath . DIRECTORY_SEPARATOR . $subPath;
        if (!is_dir($pathname)) {
            mkdir($pathname, 0775, true);
        }
    }
}
