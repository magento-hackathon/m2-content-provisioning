<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser\Query;

use Magento\Framework\Exception\LocalizedException;

class FetchMediaFilesFromContent
{
    /**
     * @param string $content
     * @return array
     * @throws LocalizedException
     */
    public function execute(string $content): array
    {
        if (preg_match_all('/\{\{media url=\&quot\;(?P<path>.*?)\&quot\;\}\}/', $content, $matches)) {
            return $matches['path'];
        }
        return [];
    }
}
