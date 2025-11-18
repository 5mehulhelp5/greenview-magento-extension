<?php
/**
 * Validate Token Controller
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use GreenView\Viewer\Service\ApiClient;

class Validate extends Action
{
    const ADMIN_RESOURCE = 'GreenView_Viewer::config';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ApiClient $apiClient
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ApiClient $apiClient
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->apiClient = $apiClient;
    }

    /**
     * Validate token
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            // Get the raw response for debugging
            $token = $this->getRequest()->getParam('token');
            if ($token) {
                // Override token temporarily for validation
                $this->apiClient->setTokenOverride($token);
            }

            $isValid = $this->apiClient->validateToken();
            $debugInfo = $this->apiClient->getLastResponse();

            if ($isValid) {
                $companyInfo = $this->apiClient->getCompanyInfo();
                $companyName = $companyInfo['name'] ?? 'Unknown';

                return $result->setData([
                    'success' => true,
                    'message' => __('Token is valid! Connected to: %1', $companyName)
                ]);
            } else {
                return $result->setData([
                    'success' => false,
                    'message' => __('Invalid token or connection error'),
                    'debug' => $debugInfo
                ]);
            }
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Error: %1', $e->getMessage()),
                'debug' => [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }
}
