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

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Model\Config\Data;
use Firegento\ContentProvisioning\Model\DTO\CmsBlockDto;
use Firegento\ContentProvisioning\Model\DTO\CmsBlockDtoFactory;

class GetFilesContentInformation
{
    /**
     * @var Data
     */
    private Data $moduleConfig;

    /**
     * @var CmsBlockDtoFactory
     */
    private CmsBlockDtoFactory $blockDtoFactory;

    /**
     * @param Data $moduleConfig
     * @param CmsBlockDtoFactory $blockDtoFactory
     */
    public function __construct(
        Data $moduleConfig,
        CmsBlockDtoFactory $blockDtoFactory
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->blockDtoFactory = $blockDtoFactory;
    }

    /**
     * @return CmsBlockDto[]
     */
    public function execute(): array
    {
        $changedBlocks = [];
        $blockList = $this->moduleConfig->getList()['blocks'];
        if (isset($blockList)) {
            foreach ($blockList as $fileData) {
                $changedBlockDto = $this->blockDtoFactory->create();
                $changedBlockDto->setIdentifier((string)$fileData['identifier']);
                $changedBlockDto->setTitle((string)$fileData['title']);
                $changedBlockDto->setKey((string)$fileData['key']);
                $changedBlockDto->setContent((string)$fileData['content']);
                $changedBlockDto->setIsActive((bool)$fileData['is_active']);
                $changedBlockDto->setStoreCodes($fileData['stores']);
                $changedBlockDto->setIsMaintained((bool)$fileData['is_maintained']);

                $changedBlocks[] = $changedBlockDto;
            }
        }
        return $changedBlocks;
    }
}
