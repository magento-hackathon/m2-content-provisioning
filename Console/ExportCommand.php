<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Console;

use Firegento\ContentProvisioning\Model\EntryBuilder;
use Firegento\ContentProvisioning\Model\Strategy;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    /**
     * @var Strategy\Provider
     */
    private $provider;
    /**
     * @var EntryBuilder
     */
    private $entryBuilder;

    public function __construct(
        Strategy\Provider $provider,
        EntryBuilder      $entryBuilder,
        string            $name
    ) {
        parent::__construct($name);

        $this->provider     = $provider;
        $this->entryBuilder = $entryBuilder;
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
        $strategy   = $this->provider->get($input->getArgument('strategy'));

        $entry = $this->entryBuilder->build($cmsType, $identifier);

        $this->export->execute($strategy, $entry);


    }
}
