<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GetFirstPageByPageEntry
{
    /**
     * @var GetPagesByPageEntry
     */
    private $getPagesByPageEntry;

    /**
     * @param GetPagesByPageEntry $getPagesByPageEntry
     */
    public function __construct(
        GetPagesByPageEntry $getPagesByPageEntry
    ) {
        $this->getPagesByPageEntry = $getPagesByPageEntry;
    }

    /**
     * @param PageEntryInterface $pageEntry
     * @return PageInterface|null
     * @throws NoSuchEntityException
     */
    public function execute(PageEntryInterface $pageEntry): ?PageInterface
    {
        $pages = $this->getPagesByPageEntry->execute($pageEntry);
        return $pages[0] ?? null;
    }
}
