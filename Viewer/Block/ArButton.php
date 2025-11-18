<?php
/**
 * AR Button Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use GreenView\Viewer\Helper\Data as Helper;
use GreenView\Viewer\Service\SplatManager;
use Magento\Framework\HTTP\Header;

class ArButton extends Template
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
     * @var Header
     */
    protected $httpHeader;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param SplatManager $splatManager
     * @param Header $httpHeader
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        SplatManager $splatManager,
        Header $httpHeader,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->splatManager = $splatManager;
        $this->httpHeader = $httpHeader;
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
     * Check if AR is enabled
     *
     * @return bool
     */
    public function isArEnabled()
    {
        $splatData = $this->getSplatData();
        if (!$splatData) {
            return false;
        }

        $arEnabled = $splatData['arEnabled'] ?? true;
        $arShortLink = $splatData['arShortLink'] ?? '';

        return $arEnabled && !empty($arShortLink);
    }

    /**
     * Get AR short link
     *
     * @return string
     */
    public function getArShortLink()
    {
        $splatData = $this->getSplatData();
        return $splatData['arShortLink'] ?? '';
    }

    /**
     * Get QR code URL
     *
     * @return string
     */
    public function getQrCodeUrl()
    {
        $arLink = $this->getArShortLink();
        return 'https://qr.green-view.nl/generate-qr?content=' . urlencode($arLink) . '&size=512&fg_color=%23000&bg_color=%23FFFFFF';
    }

    /**
     * Check if mobile device
     *
     * @return bool
     */
    public function isMobile()
    {
        return $this->httpHeader->getHttpUserAgent() &&
               preg_match('/(android|iphone|ipad|mobile)/i', $this->httpHeader->getHttpUserAgent());
    }

    /**
     * Get button text
     *
     * @return string
     */
    public function getButtonText()
    {
        return $this->getData('text') ?: 'Discover AR';
    }

    /**
     * Get background color
     *
     * @return string
     */
    public function getBgColor()
    {
        return $this->getData('bg_color') ?: '#38585a';
    }

    /**
     * Get border color
     *
     * @return string
     */
    public function getBorderColor()
    {
        return $this->getData('border_color') ?: '#38585a';
    }

    /**
     * Get text color
     *
     * @return string
     */
    public function getTextColor()
    {
        return $this->getData('text_color') ?: '#38585a';
    }

    /**
     * Get button width
     *
     * @return string
     */
    public function getButtonWidth()
    {
        return $this->getData('width') ?: '235px';
    }

    /**
     * Get custom class
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
}
