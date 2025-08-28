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

use Magento\Cms\Model\Block;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class GetCmsBlockByIdentifier
{
    public const CMS_BLOCK_TABLE_NAME = 'cms_block';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * @param string $blockIdentifier
     * @return Block[]
     */
    public function execute(string $blockIdentifier): array
    {
        try {
            return $this->getBlocks($blockIdentifier);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }

    /**
     * Get CMS Blocks data by its identifier.
     *
     * @param string $blockIdentifier
     * @return string[]
     */
    private function getBlocks(string $blockIdentifier): array
    {
        $connection = $this->resource->getConnection();
        $select =  $connection->select()
            ->from(self::CMS_BLOCK_TABLE_NAME)
            ->where('identifier = :identifier');

        $bind = ['identifier' => $blockIdentifier];
        return $connection->fetchAll($select, $bind);
    }
}
