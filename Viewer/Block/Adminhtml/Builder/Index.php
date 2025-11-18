<?php
/**
 * Shortcode Builder Block
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Block\Adminhtml\Builder;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use GreenView\Viewer\Model\ResourceModel\Splat\CollectionFactory;
use GreenView\Viewer\Helper\Data as GreenViewHelper;

class Index extends Template
{
    /**
     * @var string
     */
    protected $_template = 'GreenView_Viewer::builder/index.phtml';

    /**
     * @var CollectionFactory
     */
    protected $splatCollectionFactory;

    /**
     * @var GreenViewHelper
     */
    protected $greenViewHelper;

    /**
     * @param Context $context
     * @param CollectionFactory $splatCollectionFactory
     * @param GreenViewHelper $greenViewHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $splatCollectionFactory,
        GreenViewHelper $greenViewHelper,
        array $data = []
    ) {
        $this->splatCollectionFactory = $splatCollectionFactory;
        $this->greenViewHelper = $greenViewHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get all available splats
     *
     * @return \GreenView\Viewer\Model\ResourceModel\Splat\Collection
     */
    public function getSplats()
    {
        return $this->splatCollectionFactory->create()
            ->addFieldToSelect(['id', 'slug', 'name', 'file_url'])
            ->addOrder('name', 'ASC');
    }

    /**
     * Check if GreenView is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->greenViewHelper->isEnabled();
    }

    /**
     * Get company token
     *
     * @return string|null
     */
    public function getCompanyToken()
    {
        return $this->greenViewHelper->getCompanyToken();
    }
}
