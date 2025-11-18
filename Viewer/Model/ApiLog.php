<?php
/**
 * API Log Model
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Model;

use Magento\Framework\Model\AbstractModel;
use GreenView\Viewer\Model\ResourceModel\ApiLog as ApiLogResource;

class ApiLog extends AbstractModel
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'greenview_api_log';

    /**
     * @var string
     */
    protected $_cacheTag = 'greenview_api_log';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'greenview_api_log';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ApiLogResource::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get request headers as array
     *
     * @return array|null
     */
    public function getRequestHeadersArray()
    {
        $headers = $this->getData('request_headers');
        if ($headers && is_string($headers)) {
            return json_decode($headers, true);
        }
        return null;
    }

    /**
     * Set request headers from array
     *
     * @param array $headers
     * @return $this
     */
    public function setRequestHeadersArray($headers)
    {
        if (is_array($headers)) {
            $this->setData('request_headers', json_encode($headers));
        }
        return $this;
    }
}
