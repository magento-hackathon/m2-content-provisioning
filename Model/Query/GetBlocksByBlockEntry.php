<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\GetBlockByIdentifier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class GetBlocksByBlockEntry
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GetBlockByIdentifier
     */
    private $getBlockByIdentifier;

    /**
     * @param GetBlockByIdentifier $getBlockByIdentifier
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GetBlockByIdentifier $getBlockByIdentifier,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->getBlockByIdentifier = $getBlockByIdentifier;
    }

    /**
     * @param BlockEntryInterface $blockEntry
     * @return PageInterface[]
     * @throws NoSuchEntityException
     */
    public function execute(BlockEntryInterface $blockEntry): array
    {
        $blocks = [];
        foreach ($blockEntry->getStores() as $storeCode) {
            $store = $this->storeManager->getStore($storeCode);
            try {
                $block = $this->getBlockByIdentifier->execute($blockEntry->getIdentifier(), (int)$store->getId());
                $blocks[] = $block;
            } catch (NoSuchEntityException $exception) {
                // This exception is expected and do not need be handled
                // @TODO Create own page loader class
            }
        }
        return $blocks;
    }
}
