<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Api\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ListCommand extends Command
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     * @param string|null $name
     */
    public function __construct(
        ConfigurationInterface $configuration,
        string $name = null
    ) {
        parent::__construct($name);
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Type', 'Identifier', 'Stores', 'Maintained', 'Content (Teaser)']);

        foreach ($this->configuration->getList() as $type => $entries) {
            foreach ($entries as $entry) {
                $table->addRow([
                    $type,
                    $entry['identifier'],
                    implode(', ', $entry['stores']),
                    $entry['maintained'] ? 'yes' : 'no',
                    substr($entry['content'], 0, 200),
                ]);
            }
        }

        $table->render($output);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('content-provisioning:list');
        $this->setDescription('List all given content provisioning definitions');
        parent::configure();
    }

}