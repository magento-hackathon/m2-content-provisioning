<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchChildNodeValue;
use Firegento\ContentProvisioning\Model\Resolver\PathResolver;

class MediaDirectoryParser implements ConfigParserInterface
{
    /**
     * @var FetchChildNodeValue
     */
    private $fetchChildNodeValue;
    /**
     * @var PathResolver
     */
    private $pathResolver;

    /**
     * @param FetchChildNodeValue $fetchChildNodeValue
     * @param PathResolver $pathResolver
     */
    public function __construct(
        FetchChildNodeValue $fetchChildNodeValue,
        PathResolver $pathResolver
    ) {
        $this->fetchChildNodeValue = $fetchChildNodeValue;
        $this->pathResolver = $pathResolver;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $nodeValue = $this->fetchChildNodeValue->execute($element, 'media_directory');

        $mediaDirectory = null;
        if (!empty($nodeValue)) {
            $mediaDirectory = $this->pathResolver->execute($nodeValue);
        }

        return [
            EntryInterface::MEDIA_DIRECTORY => $mediaDirectory
        ];
    }
}
