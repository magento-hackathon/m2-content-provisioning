<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class StoreIdsByStoreCodeResolver
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
     * @param array $storeCodes
     * @return int[]
     * @throws NoSuchEntityException
     */
    public function execute(array $storeCodes): array
    {
        $storeIds = [Store::DEFAULT_STORE_ID];
        foreach ($storeCodes as $storeCode) {
            $storeIds[] = $this->storeManager->getStore($storeCode)->getId();
        }
        return $storeIds;
    }
}
