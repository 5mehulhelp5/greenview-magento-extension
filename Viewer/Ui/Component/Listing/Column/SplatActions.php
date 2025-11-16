<?php
/**
 * Splat Actions Column
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use GreenView\Viewer\Helper\Data as Helper;

class SplatActions extends Column
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Helper $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Helper $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->helper = $helper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $companyToken = $this->helper->getCompanyToken();

            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id']) && isset($item['slug'])) {
                    $actions = [];

                    // Preview button - opens in new tab
                    if (!empty($item['slug']) && !empty($companyToken)) {
                        $previewUrl = 'https://dashboard.green-view.nl/viewer/' .
                                     urlencode($companyToken) . '/' .
                                     urlencode($item['slug']);

                        $actions['preview'] = [
                            'href' => $previewUrl,
                            'label' => __('Preview'),
                            'target' => '_blank'
                        ];

                        // AR button - opens in new tab
                        $arUrl = 'https://dashboard.green-view.nl/ar/' .
                                urlencode($companyToken) . '/' .
                                urlencode($item['slug']);

                        $actions['ar'] = [
                            'href' => $arUrl,
                            'label' => __('AR'),
                            'target' => '_blank'
                        ];
                    }

                    $item[$this->getData('name')] = $actions;
                }
            }
        }

        return $dataSource;
    }
}
