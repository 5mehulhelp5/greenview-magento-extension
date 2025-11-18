<?php
/**
 * Last Sync Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Adminhtml\Splats;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;

class LastSync extends Template
{
    const CONFIG_PATH_LAST_SYNC = 'greenview_viewer/sync/last_sync_time';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $_template = 'GreenView_Viewer::splats/last_sync.phtml';

    /**
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get last sync timestamp
     *
     * @return int|null
     */
    public function getLastSyncTime()
    {
        $timestamp = $this->scopeConfig->getValue(
            self::CONFIG_PATH_LAST_SYNC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $timestamp ? (int)$timestamp : null;
    }

    /**
     * Get formatted last sync time
     *
     * @return string
     */
    public function getFormattedLastSync()
    {
        $timestamp = $this->getLastSyncTime();

        if (!$timestamp) {
            return 'Never';
        }

        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ($minutes === 1 ? ' minute ago' : ' minutes ago');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ($hours === 1 ? ' hour ago' : ' hours ago');
        } else {
            $days = floor($diff / 86400);
            return $days . ($days === 1 ? ' day ago' : ' days ago');
        }
    }
}
