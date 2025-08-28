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

namespace Firegento\ContentProvisioning\Model\Query;

class GetDbBlockIds
{
    /**
     * @var GetBlockEntryByKey
     */
    private GetBlockEntryByKey $getBlockEntryByKey;

    /**
     * @var GetBlocksByBlockEntry
     */
    private GetBlocksByBlockEntry $getBlocksByBlockEntry;

    /**
     * @param GetBlockEntryByKey $getBlockEntryByKey
     * @param GetBlocksByBlockEntry $getBlocksByBlockEntry
     */
    public function __construct(
        GetBlockEntryByKey $getBlockEntryByKey,
        GetBlocksByBlockEntry $getBlocksByBlockEntry,
    ) {
        $this->getBlockEntryByKey = $getBlockEntryByKey;
        $this->getBlocksByBlockEntry = $getBlocksByBlockEntry;
    }

    /**
     * Return block IDs from database
     * @param string $key
     * @return string[]
     */
    public function execute(string $key): array
    {
        $ids = [];
        $blockEntry = $this->getBlockEntryByKey->get($key);
        $blocks = $this->getBlocksByBlockEntry->execute($blockEntry);
        foreach ($blocks as $block) {
            $ids[] = $block->getId();
        }
        return $ids;
    }
}
