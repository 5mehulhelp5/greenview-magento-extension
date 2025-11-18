<?php
/**
 * Data Helper
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'greenview_viewer/general/enabled';
    const XML_PATH_COMPANY_TOKEN = 'greenview_viewer/general/company_token';
    const XML_PATH_SANDBOX_MODE = 'greenview_viewer/general/sandbox_mode';
    const XML_PATH_CACHE_LIFETIME = 'greenview_viewer/cache/cache_lifetime';

    const PRODUCTION_API_URL = 'https://api.green-view.nl/integration';
    const SANDBOX_API_URL = 'https://api-stg.green-view.nl/integration';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get company token
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyToken($storeId = null)
    {
        $token = $this->scopeConfig->getValue(
            self::XML_PATH_COMPANY_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $token ? $this->encryptor->decrypt($token) : '';
    }

    /**
     * Check if sandbox mode is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isSandboxMode($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SANDBOX_MODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get API base URL
     *
     * @param int|null $storeId
     * @return string
     */
    public function getApiBaseUrl($storeId = null)
    {
        return $this->isSandboxMode($storeId) ? self::SANDBOX_API_URL : self::PRODUCTION_API_URL;
    }

    /**
     * Get cache lifetime in seconds
     *
     * @param int|null $storeId
     * @return int
     */
    public function getCacheLifetime($storeId = null)
    {
        $lifetime = $this->scopeConfig->getValue(
            self::XML_PATH_CACHE_LIFETIME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $lifetime !== null ? (int)$lifetime : 86400; // Default 24 hours
    }
}
