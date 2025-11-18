<?php
/**
 * Product View Block for 3D Viewer
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use GreenView\Viewer\Helper\Data as GreenViewHelper;
use GreenView\Viewer\Service\SplatManager;

class View extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var GreenViewHelper
     */
    protected $greenViewHelper;

    /**
     * @var SplatManager
     */
    protected $splatManager;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param GreenViewHelper $greenViewHelper
     * @param SplatManager $splatManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        GreenViewHelper $greenViewHelper,
        SplatManager $splatManager,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->greenViewHelper = $greenViewHelper;
        $this->splatManager = $splatManager;
        parent::__construct($context, $data);
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Check if 3D viewer is enabled for this product
     *
     * @return bool
     */
    public function is3DEnabled()
    {
        $product = $this->getProduct();
        if (!$product) {
            return false;
        }

        return (bool)$product->getData('greenview_enable_3d');
    }

    /**
     * Check if AR button is enabled for this product
     *
     * @return bool
     */
    public function isAREnabled()
    {
        $product = $this->getProduct();
        if (!$product) {
            return false;
        }

        return (bool)$product->getData('greenview_enable_ar');
    }

    /**
     * Get splat slug for this product
     *
     * @return string|null
     */
    public function getSplatSlug()
    {
        $product = $this->getProduct();
        if (!$product) {
            return null;
        }

        return $product->getData('greenview_splat_slug');
    }

    /**
     * Get splat data
     *
     * @return array|null
     */
    public function getSplatData()
    {
        $slug = $this->getSplatSlug();
        if (!$slug) {
            return null;
        }

        return $this->splatManager->getSplatData($slug);
    }

    /**
     * Get file URL for the splat
     *
     * @return string|null
     */
    public function getFileUrl()
    {
        $splatData = $this->getSplatData();
        if (!$splatData || !isset($splatData['file_url'])) {
            return null;
        }

        return $splatData['file_url'];
    }

    /**
     * Check if GreenView is enabled globally
     *
     * @return bool
     */
    public function isGreenViewEnabled()
    {
        return $this->greenViewHelper->isEnabled();
    }

    /**
     * Check if any GreenView feature is enabled
     *
     * @return bool
     */
    public function shouldDisplay()
    {
        return ($this->is3DEnabled() || $this->isAREnabled()) && $this->getSplatSlug();
    }
}
