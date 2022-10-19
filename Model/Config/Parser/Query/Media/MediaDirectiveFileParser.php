<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser\Query\Media;

use Firegento\ContentProvisioning\Api\MediaFilesParserInterface;

class MediaDirectiveFileParser implements MediaFilesParserInterface
{
    /**
     * Parse media files from {media} directives
     *
     * @param string $content
     * @return array
     */
    public function execute(string $content): array
    {
        if (preg_match_all('/\{\{media url=(?P<path>.*?)\}\}/', $content, $matches)) {
            return $matches['path'];
        }

        return [];
    }
}
