<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir\Reader;

class PathResolver
{
    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param Reader $moduleReader
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Reader $moduleReader,
        DirectoryList $directoryList
    ) {
        $this->moduleReader = $moduleReader;
        $this->directoryList = $directoryList;
    }

    /**
     * @param string $path
     * @return string
     */
    public function execute(string $path): string
    {
        if (strpos($path, '::') !== false) {
            $pathParts = explode('::', $path, 2);
            $moduleName = $pathParts[0];
            $filePath = $pathParts[1];
            $moduleDirectory = $this->moduleReader->getModuleDir('', $moduleName);
            return implode(DIRECTORY_SEPARATOR, [
                $moduleDirectory,
                $filePath
            ]);
        } else {
            return implode(DIRECTORY_SEPARATOR, [
                $this->directoryList->getRoot(),
                $path
            ]);
        }
    }
}