<?php
/**
 * Clear Logs Controller
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use GreenView\Viewer\Model\ResourceModel\ApiLog\CollectionFactory;

class Clear extends Action
{
    const ADMIN_RESOURCE = 'GreenView_Viewer::logs';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Clear all logs
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->collectionFactory->create();
            $count = $collection->getSize();

            foreach ($collection as $log) {
                $log->delete();
            }

            $this->messageManager->addSuccessMessage(
                __('Successfully cleared %1 log(s)', $count)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error clearing logs: %1', $e->getMessage())
            );
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}
