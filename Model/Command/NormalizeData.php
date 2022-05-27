<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Command;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class NormalizeData
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
     * Normalize entry data in order to pass them to
     * CMS entity model like Block or Page
     *
     * @param array $data
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(array $data): array
    {
        $storeIds = [];
        $storeCodes = $data[EntryInterface::STORES] ?? [];
        foreach ($storeCodes as $code) {
            $storeIds[] = $this->storeManager->getStore($code)->getId();
        }
        $data[EntryInterface::STORES] = $storeIds;
        $data['store_id'] = $storeIds;

        unset($data[EntryInterface::IS_MAINTAINED]);
        unset($data[EntryInterface::KEY]);

        return $data;
    }
}
