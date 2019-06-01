<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model;

use Firegento\ContentProvisioning\Api\Data\EntryInterface;
use Firegento\ContentProvisioning\Model\Builder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

class EntryBuilder
{
    /**
     * @var Builder\Page
     */
    private $pageBuilder;
    /**
     * @var Builder\Block
     */
    private $blockBuilder;

    public function __construct(
        Builder\Page  $pageBuilder,
        Builder\Block $blockBuilder
    ) {
        $this->pageBuilder = $pageBuilder;
        $this->blockBuilder = $blockBuilder;
    }

    /**
     * @param string $cmsType
     * @param string $identifier
     * @return EntryInterface
     * @throws InputException
     * @throws LocalizedException
     */
    public function build(string $cmsType, string $identifier): EntryInterface
    {
        switch ($cmsType) {
            case 'page':
                return $this->pageBuilder->build($identifier);
            case 'block':
                return $this->blockBuilder->build($identifier);
            default:
                throw new InputException(__('Cms Type: %cms_type not found.', ['cms_type' => $cmsType]));
        }
    }
}