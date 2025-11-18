<?php
/**
 * Viewer Widget Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Widget;

use GreenView\Viewer\Block\Viewer as ViewerBlock;
use Magento\Widget\Block\BlockInterface;

class Viewer extends ViewerBlock implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'GreenView_Viewer::viewer.phtml';
}
