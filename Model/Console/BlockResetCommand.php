<?php
/**
 * Copyright (c) 2021 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see http://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Model\Command\ApplyBlockEntry;
use Firegento\ContentProvisioning\Model\Command\ApplyMediaFiles;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright  Copyright (c) 2021 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 *
 * @link       https://www.techdivision.com/
 * @author     Team Zero <zero@techdivision.com>
 */
class BlockResetCommand extends Command
{
    public const COMMAND          = 'content-provisioning:block:reset';
    public const PARAM_KEY        = 'key';
    public const PARAM_IDENTIFIER = 'identifier';

    private GetBlockEntryList $getBlockEntryList;
    private ApplyBlockEntry $applyBlockEntry;
    private ApplyMediaFiles $applyMediaFiles;

    /**
     * @param GetBlockEntryList $getBlockEntryList
     * @param ApplyBlockEntry $applyBlockEntry
     * @param ApplyMediaFiles $applyMediaFiles
     */
    public function __construct(
        GetBlockEntryList $getBlockEntryList,
        ApplyBlockEntry $applyBlockEntry,
        ApplyMediaFiles $applyMediaFiles
    ) {
        parent::__construct();

        $this->getBlockEntryList = $getBlockEntryList;
        $this->applyBlockEntry   = $applyBlockEntry;
        $this->applyMediaFiles   = $applyMediaFiles;
    }

    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND);
        $this->setDescription('Reset CMS content');
        $this->addOption(self::PARAM_KEY, 'k', InputOption::VALUE_OPTIONAL, 'Key');
        $this->addOption(self::PARAM_IDENTIFIER, 'i', InputOption::VALUE_OPTIONAL, 'Identifier');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            [$search, $type] = $this->parseParams($input);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>' . "\n");
            return Cli::RETURN_FAILURE;
        }

        $blockEntries = $this->getBlockEntries($search, $type);
        $updateCount  = 0;

        if (empty($blockEntries)) {
            $output->writeln('<error>block entry not found for ' . $type . ' "' . $search . '"</error>' . "\n");
            return Cli::RETURN_FAILURE;
        }

        try {
            foreach ($blockEntries as $blockEntry) {
                $this->applyBlockEntry->execute($blockEntry);
                $this->applyMediaFiles->execute($blockEntry);
                $updateCount++;
            }
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>' . "\n");
            return Cli::RETURN_FAILURE;
        }

        $pluralS = ($updateCount > 1) ? 's' : '';
        $output->writeln('<info>' . $updateCount . ' block' . $pluralS . ' successfully updated</info>');

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return string[]
     * @throws \Exception
     */
    private function parseParams(InputInterface $input): array
    {
        $key        = $input->getOption(self::PARAM_KEY);
        $identifier = $input->getOption(self::PARAM_IDENTIFIER);

        if ((!empty($key)) && (!empty($identifier))) {
            throw new \Exception('Provide either "' . self::PARAM_KEY . '" or "' . self::PARAM_IDENTIFIER . '", not both!');
        }

        if (!empty($key)) {
            $search = $key;
            $type   = self::PARAM_KEY;
        } elseif (!empty($identifier)) {
            $search = $identifier;
            $type   = self::PARAM_IDENTIFIER;
        } else {
            throw new \Exception('Provide either "' . self::PARAM_KEY . '" or "' . self::PARAM_IDENTIFIER . '"!');
        }

        return [$search, $type];
    }

    /**
     * @param string $search
     * @param string $type
     * @return BlockEntryInterface[]
     */
    private function getBlockEntries(string $search, string $type = self::PARAM_KEY): array
    {
        if (!in_array($type, [self::PARAM_KEY, self::PARAM_IDENTIFIER])) {
            return [];
        }

        $method  = 'get' . ucfirst($type);
        $entries = [];

        foreach ($this->getBlockEntryList->get() as $blockEntry) {
            if ($blockEntry->$method() === $search) {
                $entries[] = $blockEntry;
            }
        }

        return $entries;
    }
}
