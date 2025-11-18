<?php
/**
 * Product Media Gallery Replacement Block
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

class Media extends Template
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
     * @var string
     */
    protected $_template = 'GreenView_Viewer::product/media.phtml';

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
     * Check if 3D product mode is enabled (replaces images)
     *
     * @return bool
     */
    public function is3DProductEnabled()
    {
        $product = $this->getProduct();
        if (!$product) {
            return false;
        }

        return (bool)$product->getData('greenview_enable_3d_product');
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
     * Get splat model by slug
     *
     * @return \GreenView\Viewer\Model\Splat|null
     */
    public function getSplat()
    {
        $slug = $this->getSplatSlug();
        if (!$slug) {
            return null;
        }

        return $this->splatManager->getSplatBySlug($slug);
    }

    /**
     * Get file URL for the splat
     *
     * @return string|null
     */
    public function getFileUrl()
    {
        $splat = $this->getSplat();
        if (!$splat) {
            return null;
        }

        return $splat->getData('file_url');
    }

    /**
     * Get thumbnail URL for the splat
     *
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        $splat = $this->getSplat();
        if (!$splat) {
            return null;
        }

        return $splat->getData('thumbnail_url');
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
     * Get company token for AR URL
     *
     * @return string|null
     */
    public function getCompanyToken()
    {
        return $this->greenViewHelper->getCompanyToken();
    }

    /**
     * Get AR URL
     *
     * @return string|null
     */
    public function getArUrl()
    {
        $token = $this->getCompanyToken();
        $slug = $this->getSplatSlug();

        if (!$token || !$slug) {
            return null;
        }

        return 'https://dashboard.green-view.nl/ar/' . $token . '/' . $slug;
    }

    /**
     * Check if should show 3D viewer in media gallery
     *
     * @return bool
     */
    public function shouldReplace()
    {
        return $this->isGreenViewEnabled()
            && $this->is3DProductEnabled()
            && $this->getSplatSlug()
            && $this->getFileUrl();
    }
}
