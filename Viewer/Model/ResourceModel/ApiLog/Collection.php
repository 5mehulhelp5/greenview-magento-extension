<?php
/**
 * API Log Collection
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Model\ResourceModel\ApiLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GreenView\Viewer\Model\ApiLog as ApiLogModel;
use GreenView\Viewer\Model\ResourceModel\ApiLog as ApiLogResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ApiLogModel::class, ApiLogResource::class);
    }
}
