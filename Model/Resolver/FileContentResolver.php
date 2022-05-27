<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use DOMElement;
use Firegento\ContentProvisioning\Api\ContentResolverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DriverInterface;

class FileContentResolver implements ContentResolverInterface
{
    /**
     * @var PathResolver
     */
    private $pathResolver;

    /**
     * @var DriverInterface
     */
    private $fileSystemDriver;

    /**
     * @param PathResolver $pathResolver
     * @param DriverInterface $fileSystemDriver
     */
    public function __construct(
        PathResolver $pathResolver,
        DriverInterface $fileSystemDriver
    ) {
        $this->pathResolver = $pathResolver;
        $this->fileSystemDriver = $fileSystemDriver;
    }

    /**
     * @param DOMElement $node
     * @return string
     * @throws LocalizedException
     */
    public function execute(DOMElement $node): string
    {
        $path = $this->pathResolver->execute((string)$node->textContent);
        if (!$this->fileSystemDriver->isFile($path)) {
            throw new LocalizedException(__('Given content file %file does not exists.', ['file' => $path]));
        }

        return $this->fileSystemDriver->fileGetContents($path);
    }
}
