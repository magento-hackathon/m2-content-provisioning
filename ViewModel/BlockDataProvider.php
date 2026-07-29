<?php
declare(strict_types=1);

/**
 * Copyright (c) 2025 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 *
 * @copyright  Copyright (c) 2025 TechDivision GmbH (https://www.techdivision.com)
 * @author     TechDivision Team Zero <zero@techdivision.com>
 * @link       https://www.techdivision.com/
 */

namespace Firegento\ContentProvisioning\ViewModel;

use Firegento\ContentProvisioning\Model\BlockEntry;
use Firegento\ContentProvisioning\Model\Query\GetDbBlockIds;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class BlockDataProvider
{
    /**
     * @var GetDbBlockIds
     */
    private GetDbBlockIds $getDbBlockIds;

    /**
     * @param GetDbBlockIds $getDbBlockIds
     */
    public function __construct(
        GetDbBlockIds $getDbBlockIds
    ) {
        $this->getDbBlockIds = $getDbBlockIds;
    }

    /**
     * Get table data
     * @param OutputInterface $output
     * @param BlockEntry[] $blockEntries
     * @return Table
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(OutputInterface $output, array $blockEntries): Table
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Identifier', 'Stores', 'Maintained', 'Active', 'Title', 'Block ID(s) in DB']);
        foreach ($blockEntries as $block) {
            $storeCodes = $block->getStores();
            $dbBlockIds = $this->getDbBlockIds->execute($block->getKey());
            $rowData = [
                $block->getKey(),
                $block->getIdentifier(),
                implode(', ', $storeCodes),
                (string)$block->isMaintained()? 'yes':'no',
                (string)$block->isActive()? 'yes':'no',
                $block->getTitle(),
                implode(',', $dbBlockIds)
            ];
            $table->addRow($rowData);
        }
        return $table;
    }
}
