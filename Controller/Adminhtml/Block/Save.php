<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Controller\Adminhtml\Block;

use Exception;
use Firegento\ContentProvisioning\Model\Command\ApplyBlockEntry;
use Firegento\ContentProvisioning\Model\Query\GetBlockEntryByBlock;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Save CMS block action.
 */
class Save extends \Magento\Cms\Controller\Adminhtml\Block implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var GetBlockEntryByBlock
     */
    private $getBlockEntryByBlock;

    /**
     * @var ApplyBlockEntry
     */
    private $applyBlockEntry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param GetBlockEntryByBlock $getBlockEntryByBlock
     * @param ApplyBlockEntry $applyBlockEntry
     * @param BlockFactory|null $blockFactory
     * @param BlockRepositoryInterface|null $blockRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        GetBlockEntryByBlock $getBlockEntryByBlock,
        ApplyBlockEntry $applyBlockEntry,
        BlockFactory $blockFactory = null,
        BlockRepositoryInterface $blockRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->blockFactory = $blockFactory
            ?: ObjectManager::getInstance()->get(BlockFactory::class);
        $this->blockRepository = $blockRepository
            ?: ObjectManager::getInstance()->get(BlockRepositoryInterface::class);
        parent::__construct($context, $coreRegistry);
        $this->getBlockEntryByBlock = $getBlockEntryByBlock;
        $this->applyBlockEntry = $applyBlockEntry;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Block::STATUS_ENABLED;
            }
            if (empty($data['block_id'])) {
                $data['block_id'] = null;
            }

            /** @var Block $model */
            $model = $this->blockFactory->create();

            $id = $this->getRequest()->getParam('block_id');
            if ($id) {
                try {
                    $model = $this->blockRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This block no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->blockRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the block.'));
                $this->dataPersistor->clear('cms_block');
                return $this->processBlockReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the block.'));
            }

            $this->dataPersistor->set('cms_block', $data);
            return $resultRedirect->setPath('*/*/edit', ['block_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the block return
     *
     * @param Block $model
     * @param array $data
     * @param ResultInterface $resultRedirect
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    private function processBlockReturn($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } elseif ($redirect === 'duplicate') {
            $duplicateModel = $this->blockFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setIdentifier($data['identifier'] . '-' . uniqid());
            $duplicateModel->setIsActive(Block::STATUS_DISABLED);
            $this->blockRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the block.'));
            $this->dataPersistor->set('cms_block', $data);
            $resultRedirect->setPath('*/*/edit', ['block_id' => $id]);
        } elseif ($redirect === 'applyDefault') {
            $block = $this->getBlockEntryByBlock->execute($model);
            $this->applyBlockEntry->execute($block);
            $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId()]);
        }
        return $resultRedirect;
    }
}
