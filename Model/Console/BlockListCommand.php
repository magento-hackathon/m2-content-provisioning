<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 *
 * @copyright  Copyright (c) 2024 TechDivision GmbH (https://www.techdivision.com)
 * @author     TechDivision Team Zero <zero@techdivision.com>
 * @link       https://www.techdivision.com/
 */

namespace Firegento\ContentProvisioning\Model\Console;

use Firegento\ContentProvisioning\Model\DTO\CmsBlockDto;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryList;
use Firegento\ContentProvisioning\Model\Query\GetChangedBlockIdentifiers;
use Firegento\ContentProvisioning\Model\Query\GetFilesContentInformation;
use Firegento\ContentProvisioning\ViewModel\BlockDataProvider;
use Firegento\ContentProvisioning\ViewModel\ChangedBlockDataProvider;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BlockListCommand extends Command
{
    public const PARAM_CHANGED_ONLY = 'changed-only';

    /**
     * @var GetBlockEntryList
     */
    private GetBlockEntryList $getAllBlockEntries;

    /**
     * @var GetChangedBlockIdentifiers
     */
    private GetChangedBlockIdentifiers $getChangedBlocksIdentifier;

    /**
     * @var ChangedBlockDataProvider
     */
    private ChangedBlockDataProvider $changedBlockDataProvider;

    /**
     * @var GetFilesContentInformation
     */
    private GetFilesContentInformation $getFilesContentInformation;

    /**
     * @var BlockDataProvider
     */
    private BlockDataProvider $blockDataProvider;

    /**
     * @param GetBlockEntryList $getAllBlockEntries
     * @param GetFilesContentInformation $getFilesContentInformation
     * @param GetChangedBlockIdentifiers $getChangedBlocksIdentifier
     * @param ChangedBlockDataProvider $changedBlockDataProvider
     * @param BlockDataProvider $blockDataProvider
     * @param string|null $name
     */
    public function __construct(
        GetBlockEntryList $getAllBlockEntries,
        GetFilesContentInformation $getFilesContentInformation,
        GetChangedBlockIdentifiers  $getChangedBlocksIdentifier,
        ChangedBlockDataProvider $changedBlockDataProvider,
        BlockDataProvider $blockDataProvider,
        ?string $name = null
    ) {
        $this->getAllBlockEntries = $getAllBlockEntries;
        $this->getFilesContentInformation = $getFilesContentInformation;
        $this->getChangedBlocksIdentifier = $getChangedBlocksIdentifier;
        $this->changedBlockDataProvider = $changedBlockDataProvider;
        $this->blockDataProvider = $blockDataProvider;
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption(self::PARAM_CHANGED_ONLY)) {
            $changedBlockDto = $this->getChangedCmsBlockEntries();
            $tableData = $this->changedBlockDataProvider->execute($output, $changedBlockDto);
            $tableData->render();
        } else {
            $blockEntries = $this->getAllBlockEntries->get();
            $tableData = $this->blockDataProvider->execute($output, $blockEntries);
            $tableData->render();
        }

        return Command::SUCCESS;
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('content-provisioning:block:list');
        $this->setDescription('List all configured CMS block entries');
        $this->addOption(
            self::PARAM_CHANGED_ONLY,
            null,
            InputOption::VALUE_NONE,
            'Parameter to list (only) changed blocks.'
        );
        parent::configure();
    }

    /**
     * @return CmsBlockDto[]
     * @throws \Exception
     */
    private function getChangedCmsBlockEntries(): array
    {
        $files = $this->getFilesContentInformation->execute();
        return $this->getChangedBlocksIdentifier->execute($files);
    }
}
