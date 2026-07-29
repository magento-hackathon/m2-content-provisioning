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

use Exception;
use Firegento\ContentProvisioning\Model\DTO\CmsBlockDto;
use Firegento\ContentProvisioning\Model\ResourceModel\GetCmsBlockByIdentifier;
use Firegento\ContentProvisioning\Model\ResourceModel\GetDbCmsBlockContent;
use Psr\Log\LoggerInterface;

class GetChangedBlockIdentifiers
{
    /**
     * @var GetCmsBlockByIdentifier
     */
    private GetCmsBlockByIdentifier $getCmsBlockByIdentifier;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var GetDbCmsBlockContent
     */
    private GetDbCmsBlockContent $getDbCmsBlockContent;

    /**
     * @param GetCmsBlockByIdentifier $getCmsBlockByIdentifier
     * @param LoggerInterface $logger
     * @param GetDbCmsBlockContent $getDbCmsBlockContent
     */
    public function __construct(
        GetCmsBlockByIdentifier $getCmsBlockByIdentifier,
        LoggerInterface         $logger,
        GetDbCmsBlockContent $getDbCmsBlockContent
    ) {
        $this->getCmsBlockByIdentifier = $getCmsBlockByIdentifier;
        $this->logger = $logger;
        $this->getDbCmsBlockContent = $getDbCmsBlockContent;
    }

    /**
     * Return array with changed blocks identifier
     *
     * @param CmsBlockDto[] $cmsBlockPaths
     * @return array
     */
    public function execute(array $cmsBlockPaths): array
    {
        $changedBlocks = [];
        try {
            foreach ($cmsBlockPaths as $cmsBlock) {
                // Get Block content and Store(s) from code
                $codeFileContent = $cmsBlock->getContent();
                $codeStoresCode = $cmsBlock->getStoreCodes();

                // Get all blocks with this identifier from database
                $dbBlocks = $this->getCmsBlockByIdentifier->execute($cmsBlock->getIdentifier());
                $dbBlockContent = $this->getDbCmsBlockContent->execute($dbBlocks, $codeStoresCode);

                if (!empty($dbBlockContent[0]) && $codeFileContent !== $dbBlockContent[0]) {
                    $changedBlocks[] = $cmsBlock;
                }
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
        return $changedBlocks;
    }
}
