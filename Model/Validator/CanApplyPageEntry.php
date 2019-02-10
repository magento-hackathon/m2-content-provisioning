<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Validator;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Model\Query\GetFirstPageByPageEntry;
use Magento\Framework\Exception\NoSuchEntityException;

class CanApplyPageEntry
{
    /**
     * @var GetFirstPageByPageEntry
     */
    private $getFirstPageByPageEntry;

    /**
     * @param GetFirstPageByPageEntry $getFirstPageByPageEntry
     */
    public function __construct(
        GetFirstPageByPageEntry $getFirstPageByPageEntry
    ) {
        $this->getFirstPageByPageEntry = $getFirstPageByPageEntry;
    }

    /**
     * @param PageEntryInterface $pageEntry
     * @return bool
     * @throws NoSuchEntityException
     */
    public function execute(PageEntryInterface $pageEntry): bool
    {
        $page = $this->getFirstPageByPageEntry->execute($pageEntry);
        return $page === null || $pageEntry->isMaintained();
    }
}
