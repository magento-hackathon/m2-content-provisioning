<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Converter;

class ContentElementKeyBuilder
{
    /**
     * @param string $identifier
     * @param array $stores
     * @param string $contentType
     * @return string
     */
    public function build(string $identifier, array $stores, string $contentType): string
    {
        return md5($identifier . $contentType . json_encode($stores));
    }
}
