<?php
/**
 * Viewer Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use GreenView\Viewer\Helper\Data as Helper;
use GreenView\Viewer\Helper\FileCache;
use GreenView\Viewer\Service\SplatManager;

class Viewer extends Template
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var SplatManager
     */
    protected $splatManager;

    /**
     * @var FileCache
     */
    protected $fileCache;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param SplatManager $splatManager
     * @param FileCache $fileCache
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        SplatManager $splatManager,
        FileCache $fileCache,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->splatManager = $splatManager;
        $this->fileCache = $fileCache;
        parent::__construct($context, $data);
    }

    /**
     * Get splat data
     *
     * @return array|null
     */
    public function getSplatData()
    {
        $id = $this->getData('id');
        $slug = $this->getData('slug');

        if ($id) {
            return $this->splatManager->getSplatData($id, true);
        } elseif ($slug) {
            return $this->splatManager->getSplatData($slug, false);
        }

        return null;
    }

    /**
     * Get splat file URL (cached)
     *
     * @return string|null
     */
    public function getSplatFileUrl()
    {
        $splatData = $this->getSplatData();
        if ($splatData) {
            return $this->fileCache->getCachedFileUrl($splatData);
        }
        return null;
    }

    /**
     * Get viewer width
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->getData('width') ?: '100%';
    }

    /**
     * Get viewer height
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->getData('height') ?: '400px';
    }

    /**
     * Check if animation is enabled
     *
     * @return bool
     */
    public function isAnimationEnabled()
    {
        return (bool)$this->getData('animate');
    }

    /**
     * Get custom CSS class
     *
     * @return string
     */
    public function getCustomClass()
    {
        return $this->getData('class') ?: '';
    }

    /**
     * Get custom style
     *
     * @return string
     */
    public function getCustomStyle()
    {
        return $this->getData('style') ?: '';
    }

    /**
     * Get position attribute
     *
     * @return string|null
     */
    public function getPosition()
    {
        $position = $this->getData('position');
        if ($position) {
            return $position;
        }

        // Try from plugin config
        $splatData = $this->getSplatData();
        if ($splatData && isset($splatData['pluginConfig']['position'])) {
            $pos = $splatData['pluginConfig']['position'];
            return implode(',', $pos);
        }

        return null;
    }

    /**
     * Get scale attribute
     *
     * @return string|null
     */
    public function getScale()
    {
        $scale = $this->getData('scale');
        if ($scale) {
            return $scale;
        }

        // Try from plugin config
        $splatData = $this->getSplatData();
        if ($splatData && isset($splatData['pluginConfig']['scale'])) {
            $scl = $splatData['pluginConfig']['scale'];
            return implode(',', $scl);
        }

        return null;
    }

    /**
     * Get initial camera position
     *
     * @return string|null
     */
    public function getInitialCameraPosition()
    {
        $position = $this->getData('initial_camera_position');
        if ($position) {
            return $position;
        }

        // Try from plugin config
        $splatData = $this->getSplatData();
        if ($splatData && isset($splatData['pluginConfig']['initialCameraPosition'])) {
            $pos = $splatData['pluginConfig']['initialCameraPosition'];
            return implode(',', $pos);
        }

        return null;
    }

    /**
     * Use observer
     *
     * @return bool
     */
    public function useObserver()
    {
        $useObserver = $this->getData('use_observer');
        return $useObserver !== 'false';
    }
}
