<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Console;

use Firegento\ContentProvisioning\Api\ExportInterface;
use Firegento\ContentProvisioning\Model\EntryBuilder;
use Firegento\ContentProvisioning\Model\Strategy\ExportToModule;
use Firegento\ContentProvisioning\Model\Strategy\ExportToModuleFactory;
use Firegento\ContentProvisioning\Model\Strategy\ExportToVar;
use Firegento\ContentProvisioning\Model\Strategy\ExportToVarFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    /**
     * @var EntryBuilder
     */
    private $entryBuilder;

    /**
     * @var ExportToModuleFactory
     */
    private $exportToModuleFactory;

    /**
     * @var ExportToVarFactory
     */
    private $exportToVarFactory;

    /**
     * @var ExportInterface
     */
    private $export;

    /**
     * @param EntryBuilder $entryBuilder
     * @param ExportInterface $export
     * @param ExportToModuleFactory $exportToModuleFactory
     * @param ExportToVarFactory $exportToVarFactory
     * @param string $name
     */
    public function __construct(
        EntryBuilder $entryBuilder,
        ExportInterface $export,
        ExportToModuleFactory $exportToModuleFactory,
        ExportToVarFactory $exportToVarFactory,
        string $name
    ) {
        parent::__construct($name);

        $this->entryBuilder = $entryBuilder;
        $this->exportToModuleFactory = $exportToModuleFactory;
        $this->exportToVarFactory = $exportToVarFactory;
        $this->export = $export;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     * @throws InputException
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('module');
        $cmsType    = $input->getArgument('cms_type');
        $identifier = $input->getArgument('identifier');

        switch ($input->getArgument('strategy')) {
            case 'var':
                $strategy = $this->exportToVarFactory->create(['data' => ['moduleName' => $moduleName]]);
                break;
            default:
                $strategy = $this->exportToModuleFactory->create(['data' => ['moduleName' => $moduleName]]);
        }

        $entry = $this->entryBuilder->build($cmsType, $identifier);
        $this->export->execute($strategy, $entry);
    }
}
