<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Console;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList\Proxy as GetBlockEntryList;
use Firegento\ContentProvisioning\Model\Query\GetBlocksByBlockEntry\Proxy as GetBlocksByBlockEntry;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class BlockListCommand extends Command
{
    /**
     * @var GetBlockEntryList
     */
    private $getAllBlockEntries;

    /**
     * @var GetBlocksByBlockEntry
     */
    private $getBlocksByBlockEntry;

    /**
     * @param GetBlockEntryList     $getAllBlockEntries
     * @param GetBlocksByBlockEntry $getBlocksByBlockEntry
     * @param string|null           $name
     */
    public function __construct(
        GetBlockEntryList     $getAllBlockEntries,
        GetBlocksByBlockEntry $getBlocksByBlockEntry,
        string                $name = null
    ) {
        parent::__construct($name);

        $this->getAllBlockEntries    = $getAllBlockEntries;
        $this->getBlocksByBlockEntry = $getBlocksByBlockEntry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Identifier', 'Stores', 'Maintained', 'Active', 'Title', 'in DB (IDs)']);

        foreach ($this->getAllBlockEntries->get() as $entry) {
            $table->addRow([
                $entry->getKey(),
                $entry->getIdentifier(),
                implode(', ', $entry->getStores()),
                $entry->isMaintained() ? 'yes' : 'no',
                $entry->isActive() ? 'yes' : 'no',
                $entry->getTitle(),
                $this->getExistsInDbValue($entry)
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

    /**
     * @param BlockEntryInterface $entry
     * @return string
     */
    private function getExistsInDbValue(BlockEntryInterface $entry): string
    {
        try {
            $ids = [];
            foreach ($this->getBlocksByBlockEntry->execute($entry) as $page) {
                $ids[] = $page->getId();
            }

            if (empty($ids)) {
                return 'no';
            }

            return 'yes (' . implode(', ', $ids) . ')';
        } catch (LocalizedException $noSuchEntityException) {
            return 'ERROR: ' . $noSuchEntityException->getMessage();
        }
    }
}
