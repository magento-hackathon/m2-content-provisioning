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

namespace Firegento\ContentProvisioning\Service;

use Firegento\ContentProvisioning\Model\Query\GetChangedBlockIdentifiers;
use Firegento\ContentProvisioning\Model\Query\GetFilesContentInformation;
use Firegento\ContentProvisioning\Model\Query\GetDbBlockIds;

class BlockQueryService
{
    /** @var GetFilesContentInformation */
    private GetFilesContentInformation $getFilesContentInformation;
    /** @var GetChangedBlockIdentifiers */
    private GetChangedBlockIdentifiers $getChangedBlockIdentifier;
    /** @var GetDbBlockIds */
    private GetDbBlockIds $getDbBlockIds;

    /**
     * @param GetFilesContentInformation $getFilesContentInformation
     * @param GetChangedBlockIdentifiers $getChangedBlockIdentifier
     * @param GetDbBlockIds $getDbBlockIds
     */
    public function __construct(
        GetFilesContentInformation $getFilesContentInformation,
        GetChangedBlockIdentifiers $getChangedBlockIdentifier,
        GetDbBlockIds $getDbBlockIds
    ) {
        $this->getFilesContentInformation = $getFilesContentInformation;
        $this->getChangedBlockIdentifier = $getChangedBlockIdentifier;
        $this->getDbBlockIds = $getDbBlockIds;
    }

    /**
     * @return array
     */
    public function getFilesContentInformation(): array
    {
        return $this->getFilesContentInformation->execute();
    }

    /**
     * @param array $filesContentInformation
     * @return array
     */
    public function getChangedBlockIdentifiers(array $filesContentInformation): array
    {
        return $this->getChangedBlockIdentifier->execute($filesContentInformation);
    }

    /**
     * @param string $key
     * @return array
     */
    public function getDbBlockIds(string $key): array
    {
        return $this->getDbBlockIds->execute($key);
    }
}
