<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\Command\ApplyBlockEntry;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryByKey;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList\Proxy as GetBlockEntryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddBlockCommand extends Command
{
    const ARG_BLOCK_KEY = 'key';

    /**
     * @var GetBlockEntryByKey
     */
    private $getBlockEntryByKey;

    /**
     * @var ApplyBlockEntry
     */
    private $applyBlockEntry;

    /**
     * @param GetBlockEntryList $getBlockEntryByKey
     * @param string|null $name
     */
    public function __construct(
        GetBlockEntryByKey $getBlockEntryByKey,
        ApplyBlockEntry $applyBlockEntry,
        string $name = null
    ) {
        parent::__construct($name);
        $this->getBlockEntryByKey = $getBlockEntryByKey;
        $this->applyBlockEntry = $applyBlockEntry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument(self::ARG_BLOCK_KEY);
        $block = $this->getBlockEntryByKey->get($key);
        $this->applyBlockEntry->execute($block);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('content-provisioning:block:apply');
        $this->setDescription('Add a block by key');
        $this->addArgument(
            self::ARG_BLOCK_KEY,
            InputArgument::REQUIRED,
            'The key of the block to apply.'
        );
        parent::configure();
    }
}
