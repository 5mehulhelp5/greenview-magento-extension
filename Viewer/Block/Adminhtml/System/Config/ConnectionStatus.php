<?php
/**
 * Connection Status Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use GreenView\Viewer\Service\ApiClient;
use GreenView\Viewer\Helper\Data as Helper;

class ConnectionStatus extends Field
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var string
     */
    protected $_template = 'GreenView_Viewer::system/config/connection_status.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ApiClient $apiClient
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ApiClient $apiClient,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->apiClient = $apiClient;
        $this->helper = $helper;
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Get company information
     *
     * @return array|null
     */
    public function getCompanyInfo()
    {
        if (!$this->helper->isEnabled() || !$this->helper->getCompanyToken()) {
            return null;
        }

        try {
            $info = $this->apiClient->getCompanyInfo();
            return $info;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if token is configured
     *
     * @return bool
     */
    public function hasToken()
    {
        return !empty($this->helper->getCompanyToken());
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->helper->isEnabled();
    }

    /**
     * Get API base URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->helper->getApiBaseUrl();
    }
}
