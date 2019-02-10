<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Setup;

use Firegento\ContentProvisioning\Model\PageInstaller;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class Recurring
 */
class Recurring implements InstallSchemaInterface
{
    /**
     * @var PageInstaller
     */
    private $pageInstaller;

    /**
     * @param PageInstaller $pageInstaller
     */
    public function __construct(
        PageInstaller $pageInstaller
    ) {
        $this->pageInstaller = $pageInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->pageInstaller->install();
    }
}
