<?php
/**
 * Sync Controller
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use GreenView\Viewer\Service\SplatManager;

class Index extends Action
{
    const ADMIN_RESOURCE = 'GreenView_Viewer::sync';

    /**
     * @var SplatManager
     */
    protected $splatManager;

    /**
     * @param Context $context
     * @param SplatManager $splatManager
     */
    public function __construct(
        Context $context,
        SplatManager $splatManager
    ) {
        parent::__construct($context);
        $this->splatManager = $splatManager;
    }

    /**
     * Execute sync
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $count = $this->splatManager->syncSplats();
            $this->messageManager->addSuccessMessage(
                __('Successfully synced %1 splat(s) from GreenView API', $count)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error syncing splats: %1', $e->getMessage())
            );
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/splats/index');
    }
}
