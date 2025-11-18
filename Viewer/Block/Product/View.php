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
use GreenView\Viewer\Block\Viewer;
use GreenView\Viewer\Block\ArButton;

class View extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Viewer
     */
    protected $viewerBlock;

    /**
     * @var ArButton
     */
    protected $arButtonBlock;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param Viewer $viewerBlock
     * @param ArButton $arButtonBlock
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Viewer $viewerBlock,
        ArButton $arButtonBlock,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->viewerBlock = $viewerBlock;
        $this->arButtonBlock = $arButtonBlock;
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
     * Get 3D viewer HTML
     *
     * @return string
     */
    public function get3DViewerHtml()
    {
        if (!$this->is3DEnabled() || !$this->getSplatSlug()) {
            return '';
        }

        $this->viewerBlock->setData('slug', $this->getSplatSlug());
        $this->viewerBlock->setData('width', '100%');
        $this->viewerBlock->setData('height', '500px');
        $this->viewerBlock->setData('animate', '0');

        return $this->viewerBlock->toHtml();
    }

    /**
     * Get AR button HTML
     *
     * @return string
     */
    public function getARButtonHtml()
    {
        if (!$this->isAREnabled() || !$this->getSplatSlug()) {
            return '';
        }

        $this->arButtonBlock->setData('slug', $this->getSplatSlug());
        $this->arButtonBlock->setData('text', 'View in AR');
        $this->arButtonBlock->setData('bg_color', '#667eea');
        $this->arButtonBlock->setData('text_color', '#ffffff');
        $this->arButtonBlock->setData('border_color', '#667eea');
        $this->arButtonBlock->setData('width', '200px');

        return $this->arButtonBlock->toHtml();
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
