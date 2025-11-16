<?php
/**
 * Get Last Sync Time Controller
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Lasttime extends Action
{
    const ADMIN_RESOURCE = 'GreenView_Viewer::splats';
    const CONFIG_PATH_LAST_SYNC = 'greenview_viewer/sync/last_sync_time';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get last sync time
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $lastSyncTime = $this->scopeConfig->getValue(
            self::CONFIG_PATH_LAST_SYNC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $result->setData([
            'timestamp' => $lastSyncTime ? (int)$lastSyncTime : null
        ]);
    }
}
