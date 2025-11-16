<?php
/**
 * GreenView 3D Viewer Module Registration
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'GreenView_Viewer',
    __DIR__
);
