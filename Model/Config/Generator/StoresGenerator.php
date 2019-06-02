<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Generator;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Model\Config\Generator\Query\GetNodeByKey;
use Firegento\ContentProvisioning\Model\Resolver\StoreCodesByStoreIdsResolver;
use SimpleXMLElement;

class StoresGenerator implements GeneratorInterface
{
    /**
     * @var GetNodeByKey
     */
    private $getNodeByKey;
    /**
     * @var StoreCodesByStoreIdsResolver
     */
    private $storeCodesByStoreIdsResolver;

    public function __construct(
        GetNodeByKey                 $getNodeByKey,
        StoreCodesByStoreIdsResolver $storeCodesByStoreIdsResolver
    ) {
        $this->getNodeByKey                 = $getNodeByKey;
        $this->storeCodesByStoreIdsResolver = $storeCodesByStoreIdsResolver;
    }

    public function execute(EntryInterface $entry, SimpleXMLElement $xml): void
    {
        $nodeEntry = $this->getNodeByKey->execute($xml, $entry->getKey());

        if (!$nodeEntry) return;

        $nodeStores = $nodeEntry->addChild('stores');

        foreach ($this->storeCodesByStoreIdsResolver->execute($entry->getStores()) as $storeCode) {
            $nodeStore = $nodeStores->addChild('store');
            $nodeStore->addAttribute('code', $storeCode);
        }
    }
}