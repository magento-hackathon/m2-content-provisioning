<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Builder;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\Block as MageCmsBlock;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class Block
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var BlockEntryInterfaceFactory
     */
    private $blockEntryInterfaceFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        BlockRepositoryInterface   $blockRepository,
        BlockEntryInterfaceFactory $blockEntryInterfaceFactory,
        SearchCriteriaBuilder      $searchCriteriaBuilder
    ) {
        $this->blockRepository            = $blockRepository;
        $this->blockEntryInterfaceFactory = $blockEntryInterfaceFactory;
        $this->searchCriteriaBuilder      = $searchCriteriaBuilder;
    }

    /**
     * @param string $identifier
     * @return BlockEntryInterface
     * @throws LocalizedException
     */
    public function build(string $identifier): BlockEntryInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('identifier', $identifier, 'eq')
            ->create();

        /** @var BlockInterface|MageCmsBlock $block */
        $block = $this->blockRepository->getList($searchCriteria);

        return $this->blockEntryInterfaceFactory->create(['data' => $block->getData()]);
    }
}