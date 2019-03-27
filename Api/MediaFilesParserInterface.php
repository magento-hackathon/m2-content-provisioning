<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Api;

interface MediaFilesParserInterface
{
    /**
     * Parse media files in CMS content
     *
     * @param string $content
     * @return array
     */
    public function execute(string $content): array;
}
