<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\Command\ApplyPageEntry;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryByKey;
use Firegento\ContentProvisioning\Model\Query\GetPageEntryList\Proxy as GetPageEntryList;
use Firegento\ContentProvisioning\Model\Query\GetPagesByPageEntry\Proxy as GetPagesByPageEntry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddPageCommand extends Command
{
    const ARG_PAGE_KEY = 'key';

    /**
     * @var GetPageEntryByKey
     */
    private $getPageEntryByKey;

    /**
     * @var ApplyPageEntry
     */
    private $applyPageEntry;

    /**
     * @param GetPageEntryList $getAllContentEntries
     * @param GetPagesByPageEntry $getPagesByPageEntry
     * @param string|null $name
     */
    public function __construct(
        GetPageEntryByKey $getPageEntryByKey,
        ApplyPageEntry $applyPageEntry,
        string $name = null
    ) {
        parent::__construct($name);
        $this->getPageEntryByKey = $getPageEntryByKey;
        $this->applyPageEntry = $applyPageEntry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument(self::ARG_PAGE_KEY);
        $page = $this->getPageEntryByKey->get($key);
        $this->applyPageEntry->execute($page);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('content-provisioning:page:apply');
        $this->setDescription('Add a page by key');
        $this->addArgument(
            self::ARG_PAGE_KEY,
            InputArgument::REQUIRED,
            'The key of the page to apply.'
        );
        parent::configure();
    }
}
