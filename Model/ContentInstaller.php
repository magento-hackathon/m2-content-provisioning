<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\ContentInstallerInterface;
use Firegento\ContentProvisioning\Model\Installer\ContentEntryInstallerProvider;
use Firegento\ContentProvisioning\Model\Query\GetAllContentEntries;
use Magento\Framework\Exception\LocalizedException;

class ContentInstaller implements ContentInstallerInterface
{
    /**
     * @var GetAllContentEntries
     */
    private $getAllContentEntries;

    /**
     * @var ContentEntryInstallerProvider
     */
    private $contentEntryInstallerProvider;

    /**
     * @param GetAllContentEntries $getAllContentEntries
     * @param ContentEntryInstallerProvider $contentEntryInstallerProvider
     */
    public function __construct(
        GetAllContentEntries $getAllContentEntries,
        ContentEntryInstallerProvider $contentEntryInstallerProvider
    ) {
        $this->getAllContentEntries = $getAllContentEntries;
        $this->contentEntryInstallerProvider = $contentEntryInstallerProvider;
    }

    /**
     * Apply all configured CMS content changes
     *
     * @return void
     * @throws LocalizedException
     */
    public function install(): void
    {
        foreach ($this->getAllContentEntries->get() as $contentEntry) {
            $installer = $this->contentEntryInstallerProvider->get($contentEntry->getType());
            $installer->install($contentEntry);
        }
    }
}