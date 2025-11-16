<?php
/**
 * Splat Collection
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Model\ResourceModel\Splat;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GreenView\Viewer\Model\Splat as SplatModel;
use GreenView\Viewer\Model\ResourceModel\Splat as SplatResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SplatModel::class, SplatResource::class);
    }
}
