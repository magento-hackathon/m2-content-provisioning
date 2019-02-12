<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Block;

use Firegento\ContentProvisioning\Model\Query\IsMaintainedContentRequested;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Phrase;

class MaintainedContentWarning extends Template
{
    /**
     * @var IsMaintainedContentRequested
     */
    private $isMaintainedContentRequested;

    /**
     * @param Context $context
     * @param IsMaintainedContentRequested $isMaintainedContentRequested
     * @param array $data
     */
    public function __construct(
        Context $context,
        IsMaintainedContentRequested $isMaintainedContentRequested,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->isMaintainedContentRequested = $isMaintainedContentRequested;
    }

    /**
     * @return bool
     */
    public function isMaintained(): bool
    {
        return $this->isMaintainedContentRequested->execute($this->getRequest());
    }

    /**
     * @return Phrase
     */
    public function getMessage(): Phrase
    {
        return __(
            'This content is maintained by software releases. ' .
            'All changes will be overwritten with next update!'
        );
    }
}
