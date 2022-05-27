<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Resolver;

use Firegento\ContentProvisioning\Api\StoreCodeResolverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class StoreCodeResolver implements StoreCodeResolverInterface
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
     * @param string $code
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(string $code): array
    {
        $output = [];

        if ($code === '*') {
            $output[] = $this->storeManager->getStore('admin')->getCode();
        } else {
            $output[] = $this->storeManager->getStore($code)->getCode();
        }

        return $output;
    }
}
