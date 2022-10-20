<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Block\Adminhtml\Page\Edit;

use Firegento\ContentProvisioning\Model\Query\HasDefaultPageConfiguration;
use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Block\Adminhtml\Page\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var HasDefaultPageConfiguration
     */
    private $hasDefaultConfiguration;

    /**
     * SaveButton constructor.
     *
     * @param Context $context
     * @param PageRepositoryInterface $pageRepository
     * @param HasDefaultPageConfiguration $hasDefaultConfiguration
     */
    public function __construct(
        Context $context,
        PageRepositoryInterface $pageRepository,
        HasDefaultPageConfiguration $hasDefaultConfiguration
    ) {
        parent::__construct($context, $pageRepository);
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
                                'targetName' => 'cms_page_form.cms_page_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'continue',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
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
                                    'targetName' => 'cms_page_form.cms_page_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'duplicate',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id_hard' => 'save_and_close',
                'label' => __('Save & Close'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'cms_page_form.cms_page_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'close',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if ($this->hasDefaultConfiguration->execute((int)$this->getPageId())) {
            $options = array_merge(
                $options,
                [
                    [
                        'is_hard' => 'apply_default',
                        'label' => __('Reset to Default & Save'),
                        'data_attribute' => [
                            'mage-init' => [
                                'buttonAdapter' => [
                                    'actions' => [
                                        [
                                            'targetName' => 'cms_page_form.cms_page_form',
                                            'actionName' => 'save',
                                            'params' => [
                                                true,
                                                [
                                                    'back' => 'applyDefault',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }

        return $options;
    }
}
