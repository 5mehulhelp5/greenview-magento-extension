<?php
/**
 * GreenView API Client Service
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Service;

use GreenView\Viewer\Helper\Data as Helper;
use GreenView\Viewer\Model\ApiLogFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class ApiClient
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ApiLogFactory
     */
    protected $apiLogFactory;

    /**
     * @var string|null
     */
    protected $tokenOverride = null;

    /**
     * @var array
     */
    protected $lastResponse = [];

    /**
     * @param Helper $helper
     * @param Curl $curl
     * @param Json $json
     * @param LoggerInterface $logger
     * @param ApiLogFactory $apiLogFactory
     */
    public function __construct(
        Helper $helper,
        Curl $curl,
        Json $json,
        LoggerInterface $logger,
        ApiLogFactory $apiLogFactory
    ) {
        $this->helper = $helper;
        $this->curl = $curl;
        $this->json = $json;
        $this->logger = $logger;
        $this->apiLogFactory = $apiLogFactory;
    }

    /**
     * Set token override for validation
     *
     * @param string $token
     * @return void
     */
    public function setTokenOverride($token)
    {
        $this->tokenOverride = $token;
    }

    /**
     * Get last API response for debugging
     *
     * @return array
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Make API request
     *
     * @param string $endpoint
     * @param string $method
     * @param array $params
     * @return array|bool
     */
    public function makeRequest($endpoint, $method = 'GET', $params = [])
    {
        if (!$this->helper->isEnabled() && !$this->tokenOverride) {
            $this->lastResponse = [
                'error' => 'Module is disabled',
                'url' => null,
                'status_code' => null,
                'response' => null
            ];
            return false;
        }

        $token = $this->tokenOverride ?: $this->helper->getCompanyToken();
        if (empty($token)) {
            $this->lastResponse = [
                'error' => 'No API token configured',
                'url' => null,
                'status_code' => null,
                'response' => null
            ];
            $this->logError('make_request', $endpoint, 'No API token configured');
            return false;
        }

        $url = $this->helper->getApiBaseUrl() . $endpoint;

        try {
            $this->curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->addHeader('x-company-token', $token);
            $this->curl->addHeader('User-Agent', 'Magento2-GreenView-Viewer/3.1.1');

            if ($method === 'POST') {
                $this->curl->post($url, $params);
            } else {
                $this->curl->get($url);
            }

            $response = $this->curl->getBody();
            $statusCode = $this->curl->getStatus();

            // Store debug info
            $this->lastResponse = [
                'url' => $url,
                'status_code' => $statusCode,
                'response_raw' => $response,
                'headers_sent' => [
                    'x-company-token' => substr($token, 0, 10) . '...',
                    'User-Agent' => 'Magento2-GreenView-Viewer/3.1.1'
                ]
            ];

            if ($statusCode !== 200) {
                $this->lastResponse['error'] = 'Non-200 response code';
                $this->logError('make_request', $url, 'Non-200 response code', $statusCode, $response);
                return false;
            }

            $data = $this->json->unserialize($response);
            $this->lastResponse['response_parsed'] = $data;
            return $data;

        } catch (\Exception $e) {
            $this->lastResponse = [
                'url' => $url,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'status_code' => null,
                'response' => null
            ];
            $this->logError('make_request', $url, $e->getMessage());
            return false;
        }
    }

    /**
     * Validate token
     *
     * @return bool
     */
    public function validateToken()
    {
        $result = $this->makeRequest('/validate');
        return $result && isset($result['status']) && $result['status'] === 'success';
    }

    /**
     * Get company info
     *
     * @return array|bool
     */
    public function getCompanyInfo()
    {
        $result = $this->makeRequest('/company');
        return $result && isset($result['data']) ? $result['data'] : false;
    }

    /**
     * Get splats (paginated)
     *
     * @param int $page
     * @param int $limit
     * @return array|bool
     */
    public function getSplats($page = 1, $limit = 50)
    {
        return $this->makeRequest('/splats?page=' . $page . '&limit=' . $limit);
    }

    /**
     * Get splat by slug
     *
     * @param string $slug
     * @return array|bool
     */
    public function getSplatBySlug($slug)
    {
        $result = $this->makeRequest('/splats/by-slug/' . urlencode($slug));
        return $result && isset($result['data']) ? $result['data'] : false;
    }

    /**
     * Get splat by ID
     *
     * @param string $id
     * @return array|bool
     */
    public function getSplatById($id)
    {
        $result = $this->makeRequest('/splats/' . urlencode($id));
        return $result && isset($result['data']) ? $result['data'] : false;
    }

    /**
     * Log API error
     *
     * @param string $operation
     * @param string $url
     * @param string $errorMessage
     * @param int|null $responseCode
     * @param string|null $responseBody
     * @return void
     */
    protected function logError($operation, $url, $errorMessage, $responseCode = null, $responseBody = null)
    {
        $this->logger->error('GreenView API Error: ' . $errorMessage, [
            'operation' => $operation,
            'url' => $url,
            'response_code' => $responseCode
        ]);

        try {
            /** @var \GreenView\Viewer\Model\ApiLog $log */
            $log = $this->apiLogFactory->create();
            $log->setData([
                'operation' => $operation,
                'url' => $url,
                'error_message' => $errorMessage,
                'response_code' => $responseCode,
                'response_body_preview' => $responseBody ? substr($responseBody, 0, 500) : null,
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);
            $log->save();
        } catch (\Exception $e) {
            $this->logger->error('Failed to save API log: ' . $e->getMessage());
        }
    }
}
