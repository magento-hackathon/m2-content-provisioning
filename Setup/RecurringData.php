<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Setup;

use Firegento\ContentProvisioning\Model\BlockInstaller;
use Firegento\ContentProvisioning\Model\PageInstaller;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class Recurring
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var PageInstaller
     */
    private $pageInstaller;

    /**
     * @var BlockInstaller
     */
    private $blockInstaller;

    /**
     * @param PageInstaller $pageInstaller
     * @param BlockInstaller $blockInstaller
     */
    public function __construct(
        PageInstaller $pageInstaller,
        BlockInstaller $blockInstaller
    ) {
        $this->pageInstaller = $pageInstaller;
        $this->blockInstaller = $blockInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->pageInstaller->install();
        $this->blockInstaller->install();
    }
}
