<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryList\Proxy as GetPageEntryList;
use Firegento\ContentProvisioning\Model\Query\GetPagesByPageEntry\Proxy as GetPagesByPageEntry;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class PageListCommand extends Command
{
    /**
     * @var GetPageEntryList
     */
    private $getAllContentEntries;

    /**
     * @var GetPagesByPageEntry
     */
    private $getPagesByPageEntry;

    /**
     * @param GetPageEntryList $getAllContentEntries
     * @param GetPagesByPageEntry $getPagesByPageEntry
     * @param string|null $name
     */
    public function __construct(
        GetPageEntryList $getAllContentEntries,
        GetPagesByPageEntry $getPagesByPageEntry,
        string $name = null
    ) {
        parent::__construct($name);
        $this->getAllContentEntries = $getAllContentEntries;
        $this->getPagesByPageEntry = $getPagesByPageEntry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Identifier', 'Stores', 'Maintained', 'Active', 'Title', 'in DB (IDs)']);

        foreach ($this->getAllContentEntries->get() as $entry) {
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
        $this->setName('content-provisioning:page:list');
        $this->setDescription('List all configured CMS page entries');
        parent::configure();
    }

    /**
     * @param PageEntryInterface $entry
     * @return string
     */
    private function getExistsInDbValue(PageEntryInterface $entry): string
    {
        try {
            $ids = [];
            foreach ($this->getPagesByPageEntry->execute($entry) as $page) {
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
