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
use Magento\Framework\App\Config\Storage\WriterInterface;

class Index extends Action
{
    const ADMIN_RESOURCE = 'GreenView_Viewer::sync';
    const CONFIG_PATH_LAST_SYNC = 'greenview_viewer/sync/last_sync_time';

    /**
     * @var SplatManager
     */
    protected $splatManager;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @param Context $context
     * @param SplatManager $splatManager
     * @param WriterInterface $configWriter
     */
    public function __construct(
        Context $context,
        SplatManager $splatManager,
        WriterInterface $configWriter
    ) {
        parent::__construct($context);
        $this->splatManager = $splatManager;
        $this->configWriter = $configWriter;
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

            // Save last sync timestamp
            $this->configWriter->save(self::CONFIG_PATH_LAST_SYNC, time());

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
