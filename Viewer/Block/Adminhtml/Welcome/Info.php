<?php
/**
 * Welcome Info Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Adminhtml\Welcome;

use Magento\Backend\Block\Template;
use GreenView\Viewer\Helper\Data;
use Magento\Framework\Module\ModuleListInterface;

class Info extends Template
{
    /**
     * @var string
     */
    protected $_template = 'GreenView_Viewer::welcome/info.phtml';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @param Template\Context $context
     * @param Data $helper
     * @param ModuleListInterface $moduleList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        ModuleListInterface $moduleList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->moduleList = $moduleList;
    }

    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        $module = $this->moduleList->getOne('GreenView_Viewer');
        return $module['setup_version'] ?? '1.0.0';
    }

    /**
     * Check if extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }

    /**
     * Check if sandbox mode is enabled
     *
     * @return bool
     */
    public function isSandboxMode()
    {
        return $this->helper->isSandboxMode();
    }

    /**
     * Check if company token is configured
     *
     * @return bool
     */
    public function hasCompanyToken()
    {
        return !empty($this->helper->getCompanyToken());
    }

    /**
     * Get settings URL
     *
     * @return string
     */
    public function getSettingsUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit', ['section' => 'greenview_viewer']);
    }

    /**
     * Get manage splats URL
     *
     * @return string
     */
    public function getManageSplatsUrl()
    {
        return $this->getUrl('greenview/splats/index');
    }

    /**
     * Get sync splats URL
     *
     * @return string
     */
    public function getSyncSplatsUrl()
    {
        return $this->getUrl('greenview/sync/index');
    }

    /**
     * Get API logs URL
     *
     * @return string
     */
    public function getApiLogsUrl()
    {
        return $this->getUrl('greenview/logs/index');
    }
}
