<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

class StoreCodesByStoreIdsResolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param int[] $storeIds
     * @return string[]
     * @throws NoSuchEntityException
     */
    public function execute(array $storeIds): array
    {
        $storeCodes = [];
        foreach ($storeIds as $storeId) {
            $storeCodes[] = $this->storeManager->getStore($storeId)->getCode();
        }
        return $storeCodes;
    }
}