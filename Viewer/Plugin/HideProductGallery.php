<?php
/**
 * Plugin to hide product gallery when 3D Product mode is enabled
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\Registry;

class HideProductGallery
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Hide gallery HTML if 3D Product mode is enabled
     *
     * @param Gallery $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(Gallery $subject, $result)
    {
        $product = $this->registry->registry('current_product');

        if ($product && $product->getData('greenview_enable_3d_product')) {
            // Return empty string to hide the gallery
            return '';
        }

        return $result;
    }
}
