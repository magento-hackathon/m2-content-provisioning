<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Installer;

use Firegento\ContentProvisioning\Api\ContentEntryInstallerInterface;
use Magento\Framework\Exception\LocalizedException;

class ContentEntryInstallerProvider
{
    /**
     * @var array
     */
    private $installer;

    /**
     * @param ContentEntryInstallerInterface[] $installer
     * @throws LocalizedException
     */
    public function __construct(
        array $installer = []
    ) {
        foreach ($installer as $contentInstaller) {
            if (!($contentInstaller instanceof ContentEntryInstallerInterface)) {
                throw new LocalizedException(__(
                    'Given installer must be an instance of %instance',
                    ['instance' => ContentEntryInstallerInterface::class]
                ));
            }
        }
        $this->installer = $installer;
    }

    /**
     * @param string $contentType
     * @return ContentEntryInstallerInterface
     * @throws LocalizedException
     */
    public function get(string $contentType): ContentEntryInstallerInterface
    {
        if (!isset($this->installer[$contentType])) {
            throw new LocalizedException(__(
                'There is no content entry installer configured for given content type %type',
                ['type' => $contentType]
            ));
        }

        return $this->installer[$contentType];
    }
}