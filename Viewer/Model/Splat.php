<?php
/**
 * Splat Model
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Model;

use Magento\Framework\Model\AbstractModel;
use GreenView\Viewer\Model\ResourceModel\Splat as SplatResource;

class Splat extends AbstractModel
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'greenview_splat';

    /**
     * @var string
     */
    protected $_cacheTag = 'greenview_splat';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'greenview_splat';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SplatResource::class);
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
     * Get plugin config as array
     *
     * @return array|null
     */
    public function getPluginConfigArray()
    {
        $config = $this->getData('plugin_config');
        if ($config && is_string($config)) {
            return json_decode($config, true);
        }
        return null;
    }

    /**
     * Get data JSON as array
     *
     * @return array|null
     */
    public function getDataJsonArray()
    {
        $data = $this->getData('data_json');
        if ($data && is_string($data)) {
            return json_decode($data, true);
        }
        return null;
    }

    /**
     * Set plugin config from array
     *
     * @param array $config
     * @return $this
     */
    public function setPluginConfigArray($config)
    {
        if (is_array($config)) {
            $this->setData('plugin_config', json_encode($config));
        }
        return $this;
    }

    /**
     * Set data JSON from array
     *
     * @param array $data
     * @return $this
     */
    public function setDataJsonArray($data)
    {
        if (is_array($data)) {
            $this->setData('data_json', json_encode($data));
        }
        return $this;
    }
}
