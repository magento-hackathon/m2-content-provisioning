<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Builder;

use Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;
use Firegento\ContentProvisioning\Api\Data\BlockEntryInterfaceFactory;
use Magento\Cms\Api\BlockRepositoryInterface;

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

    public function __construct(
        BlockRepositoryInterface   $blockRepository,
        BlockEntryInterfaceFactory $blockEntryInterfaceFactory
    ) {
        $this->blockRepository = $blockRepository;
        $this->blockEntryInterfaceFactory = $blockEntryInterfaceFactory;
    }

    public function build(string $identifier): BlockEntryInterface
    {

    }
}