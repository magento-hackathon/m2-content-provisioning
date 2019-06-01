<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Builder;

use Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use Firegento\ContentProvisioning\Api\Data\PageEntryInterfaceFactory;
use Magento\Cms\Api\PageRepositoryInterface;

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

    public function __construct(
        PageRepositoryInterface   $pageRepository,
        PageEntryInterfaceFactory $pageEntryInterfaceFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageEntryInterfaceFactory = $pageEntryInterfaceFactory;
    }

    public function build(string $identifier): PageEntryInterface
    {

    }
}