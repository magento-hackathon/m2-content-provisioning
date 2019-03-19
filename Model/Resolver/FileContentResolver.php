<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use DOMElement;
use Firegento\ContentProvisioning\Api\ContentResolverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir\Reader;

class FileContentResolver implements ContentResolverInterface
{
    /**
     * @var PathResolver
     */
    private $pathResolver;

    /**
     * @param PathResolver $pathResolver
     */
    public function __construct(
        PathResolver $pathResolver
    ) {
        $this->pathResolver = $pathResolver;
    }

    /**
     * @param DOMElement $node
     * @return string
     * @throws LocalizedException
     */
    public function execute(DOMElement $node): string
    {
        $path = $this->pathResolver->execute((string)$node->textContent);
        if (!is_file($path)) {
            throw new LocalizedException(__('Given content file %file does not exists.', ['file' => $path]));
        }

        return file_get_contents($path);
    }
}