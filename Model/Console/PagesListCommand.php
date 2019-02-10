<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\Query\GetAllPageEntries;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class PagesListCommand extends Command
{
    /**
     * @var GetAllPageEntries
     */
    private $getAllContentEntries;

    /**
     * @param GetAllPageEntries $getAllContentEntries
     * @param string|null $name
     */
    public function __construct(
        GetAllPageEntries $getAllContentEntries,
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
        $table->setHeaders(['Key', 'Identifier', 'Stores', 'Maintained', 'Title', 'Content (Teaser)']);

        foreach ($this->getAllContentEntries->get() as $entry) {
            $table->addRow([
                $entry->getKey(),
                $entry->getIdentifier(),
                implode(', ', $entry->getStores()),
                $entry->isMaintained() ? 'yes' : 'no',
                $entry->getTitle(),
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
        $this->setName('content-provisioning:pages:list');
        $this->setDescription('List all given content provisioning definitions');
        parent::configure();
    }

}