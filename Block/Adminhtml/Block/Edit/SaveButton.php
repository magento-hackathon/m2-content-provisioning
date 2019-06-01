<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Block\Adminhtml\Block\Edit;

use Firegento\ContentProvisioning\Model\Query\HasDefaultBlockConfiguration;
use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Block\Adminhtml\Block\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var HasDefaultBlockConfiguration
     */
    private $hasDefaultConfiguration;

    /**
     * SaveButton constructor.
     *
     * @param Context $context
     * @param BlockRepositoryInterface $blockRepository
     * @param HasDefaultBlockConfiguration $hasDefaultConfiguration
     */
    public function __construct(
        Context $context,
        BlockRepositoryInterface $blockRepository,
        HasDefaultBlockConfiguration $hasDefaultConfiguration
    ) {
        parent::__construct($context, $blockRepository);
        $this->hasDefaultConfiguration = $hasDefaultConfiguration;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'cms_block_form.cms_block_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'continue'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    private function getOptions()
    {
        $options = [
            [
                'label' => __('Save & Duplicate'),
                'id_hard' => 'save_and_duplicate',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'cms_block_form.cms_block_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'duplicate'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id_hard' => 'save_and_close',
                'label' => __('Save & Close'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'cms_block_form.cms_block_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'close'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($this->hasDefaultConfiguration->get((int)$this->getBlockId())) {
            $options = array_merge($options, [[
                'is_hard' => 'apply_default',
                'label' => __('Reset to Default & Save'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'cms_block_form.cms_block_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'applyDefault'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]]]);
        }

        return $options;
    }
}
