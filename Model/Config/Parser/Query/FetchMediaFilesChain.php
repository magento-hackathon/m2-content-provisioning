<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser\Query;

use Firegento\ContentProvisioning\Api\MediaFilesParserInterface;
use Magento\Framework\Exception\LocalizedException;

class FetchMediaFilesChain implements MediaFilesParserInterface
{
    /**
     * @var array
     */
    protected $parsers;

    /**
     * @param array $parsers
     * @throws LocalizedException
     */
    public function __construct(array $parsers)
    {
        foreach ($parsers as $parserInstance) {
            if (!($parserInstance instanceof MediaFilesParserInterface)) {
                throw new LocalizedException(
                    __(
                        'Parser needs to be instance of %interface',
                        ['interface' => MediaFilesParserInterface::class]
                    )
                );
            }
        }

        $this->parsers = $parsers;
    }

    /**
     * Parse media files from CMS content delegating the parsing strategy to child components
     *
     * @param string $content
     * @return array
     */
    public function execute(string $content): array
    {
        $mediaFiles = [];

        foreach ($this->parsers as $parser) {
            $mediaFiles[] = $parser->execute($content);
        }

        return array_merge(...$mediaFiles);
    }
}
