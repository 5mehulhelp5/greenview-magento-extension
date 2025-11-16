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

class SplatActions extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'view' => [
                            'label' => __('View Details'),
                            'callback' => [
                                [
                                    'provider' => 'greenview_splats_listing.greenview_splats_listing.greenview_splats_columns.actions',
                                    'target' => 'viewSplatDetails',
                                    'params' => [
                                        'id' => $item['id']
                                    ]
                                ]
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
