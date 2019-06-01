<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Builder;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterfaceFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page as MageCmsPage;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class Page
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;
    /**
     * @var PageEntryInterfaceFactory
     */
    private $pageEntryInterfaceFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        PageRepositoryInterface   $pageRepository,
        PageEntryInterfaceFactory $pageEntryInterfaceFactory,
        SearchCriteriaBuilder     $searchCriteriaBuilder
    ) {
        $this->pageRepository            = $pageRepository;
        $this->pageEntryInterfaceFactory = $pageEntryInterfaceFactory;
        $this->searchCriteriaBuilder     = $searchCriteriaBuilder;
    }

    /**
     * @param string $identifier
     * @return PageEntryInterface
     * @throws LocalizedException
     */
    public function build(string $identifier): PageEntryInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('identifier', $identifier, 'eq')
            ->create();

        /** @var PageInterface|MageCmsPage $page */
        $page = array_shift($this->pageRepository->getList($searchCriteria)->getItems());

        return $this->pageEntryInterfaceFactory->create(['data' => $page->getData()]);
    }
}