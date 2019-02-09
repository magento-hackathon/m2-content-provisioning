<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Setup;

use Firegento\ContentProvisioning\Api\ContentInstallerInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class Recurring
 */
class Recurring implements InstallSchemaInterface
{
    /**
     * @var ContentInstallerInterface
     */
    private $contentInstaller;

    /**
     * @param ContentInstallerInterface $contentInstaller
     */
    public function __construct(
        ContentInstallerInterface $contentInstaller
    ) {
        $this->contentInstaller = $contentInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->contentInstaller->install();
    }
}
