<?php
/**
 * AR Button Widget Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Widget;

use GreenView\Viewer\Block\ArButton as ArButtonBlock;
use Magento\Widget\Block\BlockInterface;

class ArButton extends ArButtonBlock implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'GreenView_Viewer::ar-button.phtml';
}
