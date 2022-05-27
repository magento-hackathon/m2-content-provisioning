<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Magento\Framework\App\RequestInterface;

class IsMaintainedContentRequested
{
    /**
     * @var IsPageMaintained
     */
    private $isPageMaintained;

    /**
     * @var IsBlockMaintained
     */
    private $isBlockMaintained;

    /**
     * @param IsPageMaintained $isPageMaintained
     * @param IsBlockMaintained $isBlockMaintained
     */
    public function __construct(
        IsPageMaintained $isPageMaintained,
        IsBlockMaintained $isBlockMaintained
    ) {
        $this->isPageMaintained = $isPageMaintained;
        $this->isBlockMaintained = $isBlockMaintained;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function execute(RequestInterface $request): bool
    {
        if ($this->isCmsPageEditFormRequested($request)) {
            return $this->isPageMaintained->execute($this->getPageIdFromRequest($request));
        }

        if ($this->isCmsBlockEditFormRequested($request)) {
            return $this->isBlockMaintained->execute($this->getBlockIdFromRequest($request));
        }

        return false;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    private function isCmsPageEditFormRequested(RequestInterface $request): bool
    {
        return $request->getModuleName() === 'cms'
            && $request->getActionName() === 'edit'
            && $request->getParam('page_id', false);
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    private function isCmsBlockEditFormRequested(RequestInterface $request): bool
    {
        return $request->getModuleName() === 'cms'
            && $request->getActionName() === 'edit'
            && $request->getParam('block_id', false);
    }

    /**
     * @param RequestInterface $request
     * @return int
     */
    private function getPageIdFromRequest(RequestInterface $request): int
    {
        return (int)$request->getParam('page_id', null);
    }

    /**
     * @param RequestInterface $request
     * @return int
     */
    private function getBlockIdFromRequest(RequestInterface $request): int
    {
        return (int)$request->getParam('block_id', null);
    }
}
