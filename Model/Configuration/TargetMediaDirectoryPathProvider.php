<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Configuration;

use Firegento\ContentProvisioning\Api\TargetMediaDirectoryPathProviderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class TargetMediaDirectoryPathProvider implements TargetMediaDirectoryPathProviderInterface
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    /**
     * {@inheritdoc}
     * @throws FileSystemException
     */
    public function get(): string
    {
        return (string)$this->directoryList->getPath(DirectoryList::MEDIA);
    }
}
