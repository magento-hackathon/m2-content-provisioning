<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\Query\GetAllBlockEntries;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class BlockListCommand extends Command
{
    /**
     * @var GetAllBlockEntries
     */
    private $getAllBlockEntries;

    /**
     * @param GetAllBlockEntries $getAllBlockEntries
     * @param string|null $name
     */
    public function __construct(
        GetAllBlockEntries $getAllBlockEntries,
        string $name = null
    ) {
        parent::__construct($name);
        $this->getAllBlockEntries = $getAllBlockEntries;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Identifier', 'Stores', 'Maintained', 'Active', 'Title', 'Content (Teaser)']);

        foreach ($this->getAllBlockEntries->get() as $entry) {
            $table->addRow([
                $entry->getKey(),
                $entry->getIdentifier(),
                implode(', ', $entry->getStores()),
                $entry->isMaintained() ? 'yes' : 'no',
                $entry->isActive() ? 'yes' : 'no',
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
        $this->setName('content-provisioning:block:list');
        $this->setDescription('List all configured CMS block entries');
        parent::configure();
    }

}