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

namespace Firegento\ContentProvisioning\Model\ResourceModel;

use Firegento\ContentProvisioning\Model\DTO\CmsBlockDto;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class GetDbCmsBlockContent
{
    public const CMS_BLOCK_STORE_TABLE_NAME = 'cms_block_store';
    public const STORE_TABLE_NAME = 'store';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        $this->storeManager = $storeManager;
        $this->resource = $resource;
    }

    /**
     * @param array $dbBlocks
     * @param string[] $codeStoreCode
     * @return string[]
     */
    public function execute(array $dbBlocks, array $codeStoreCode): array
    {
        return $this->getCmsBlockContent($dbBlocks, $codeStoreCode);
    }

    /**
     * @param CmsBlockDto[] $blocks
     * @param string[] $storesCode
     * @return string[]
     */
    public function getCmsBlockContent(array $blocks, array $storesCode): array
    {
        $dbBlockContent = [];
        $dbStoreCode = '';

        foreach ($blocks as $dbBlock) {
            if (!$dbBlock['row_id']) {
                $dbStoreIds = $this->getStoreIdsByBlockId((int)$dbBlock['block_id']);
                foreach ($dbStoreIds as $storeId) {
                    $dbStoreCode = $storeId['store_code'];
                }
            } else {
                $dbStoreIds = $this->getStoreIdsByRowId((int)$dbBlock['row_id']);
                foreach ($dbStoreIds as $storeId) {
                    $dbStoreCode = $this->storeManager->getStore($storeId)->getCode();
                }
            }

            foreach ($storesCode as $storeCode) {
                if ($storeCode === $dbStoreCode) {
                    $dbBlockContent[] = $dbBlock['content'];
                }
            }
        }
        return $dbBlockContent;
    }

    /**
     * Get CMS Block store id by CMS Block row id
     *
     * @param int $rowId
     * @return int[]
     */
    private function getStoreIdsByRowId(int $rowId): array
    {
        $connection = $this->resource->getConnection();

        $select = $connection->select()
            ->from(self::CMS_BLOCK_STORE_TABLE_NAME, ['store_id'])
            ->where('row_id = ?', $rowId);

        return $connection->fetchCol($select);
    }

    /**
     * @param int $blockId
     * @return int[]
     */
    private function getStoreIdsByBlockId(int $blockId): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from(['cbs' => self::CMS_BLOCK_STORE_TABLE_NAME], ['store_id'])
            ->joinLeft(['s' => self::STORE_TABLE_NAME], 'cbs.store_id = s.store_id', ['store_code' => 's.code'])
            ->where('cbs.row_id = ?', $blockId);

        return $connection->fetchAll($select);
    }
}
