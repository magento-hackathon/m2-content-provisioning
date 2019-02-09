<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\ContentResolverInterface;
use Magento\Framework\Exception\LocalizedException;

class ContentResolverProvider
{
    /**
     * @var array
     */
    private $contentResolvers;

    /**
     * @param array $contentResolvers
     * @throws LocalizedException
     */
    public function __construct(
        array $contentResolvers = []
    ) {
        foreach ($contentResolvers as $resolver) {
            if (!($resolver instanceof ContentResolverInterface)) {
                throw new LocalizedException(__(
                    'Given resolver must be an instance of :interface',
                    ['interface' => ContentResolverInterface::class]
                ));
            }
        }
        $this->contentResolvers = $contentResolvers;
    }

    /**
     * @param string $typeCode
     * @return ContentResolverInterface
     * @throws LocalizedException
     */
    public function get(string $typeCode): ContentResolverInterface
    {
        if (!isset($this->contentResolvers[$typeCode])) {
            throw new LocalizedException(__(
                'There is no content resolver defined for given type code :code',
                ['code' => $typeCode]
            ));
        }

        return $this->contentResolvers[$typeCode];
    }
}