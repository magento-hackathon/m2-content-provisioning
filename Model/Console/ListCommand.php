<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\Query\GetAllContentEntries;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ListCommand extends Command
{
    /**
     * @var GetAllContentEntries
     */
    private $getAllContentEntries;

    /**
     * @param GetAllContentEntries $getAllContentEntries
     * @param string|null $name
     */
    public function __construct(
        GetAllContentEntries $getAllContentEntries,
        string $name = null
    ) {
        parent::__construct($name);
        $this->getAllContentEntries = $getAllContentEntries;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Type', 'Identifier', 'Stores', 'Maintained', 'Content (Teaser)']);

        foreach ($this->getAllContentEntries->get() as $entry) {
            $table->addRow([
                $entry->getKey(),
                $entry->getType(),
                $entry->getIdentifier(),
                implode(', ', $entry->getStores()),
                $entry->isMaintained() ? 'yes' : 'no',
                substr($entry->getContent(), 0, 147) . '...',
            ]);
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