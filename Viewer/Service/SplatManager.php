<?php
/**
 * Splat Manager Service
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Angga Pixa
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Service;

use GreenView\Viewer\Model\SplatFactory;
use GreenView\Viewer\Model\ResourceModel\Splat as SplatResource;
use GreenView\Viewer\Model\ResourceModel\Splat\CollectionFactory as SplatCollectionFactory;
use GreenView\Viewer\Service\ApiClient;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class SplatManager
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @var SplatFactory
     */
    protected $splatFactory;

    /**
     * @var SplatResource
     */
    protected $splatResource;

    /**
     * @var SplatCollectionFactory
     */
    protected $splatCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param ApiClient $apiClient
     * @param SplatFactory $splatFactory
     * @param SplatResource $splatResource
     * @param SplatCollectionFactory $splatCollectionFactory
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ApiClient $apiClient,
        SplatFactory $splatFactory,
        SplatResource $splatResource,
        SplatCollectionFactory $splatCollectionFactory,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->apiClient = $apiClient;
        $this->splatFactory = $splatFactory;
        $this->splatResource = $splatResource;
        $this->splatCollectionFactory = $splatCollectionFactory;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Sync splats from API
     *
     * @return int Number of splats synced
     * @throws \Exception
     */
    public function syncSplats()
    {
        $allSplats = [];
        $page = 1;
        $limit = 50;
        $hasMore = true;

        // Fetch all pages
        while ($hasMore) {
            $response = $this->apiClient->getSplats($page, $limit);

            if (!$response || !isset($response['data'])) {
                break;
            }

            $allSplats = array_merge($allSplats, $response['data']);

            $hasMore = isset($response['meta']) && $page < $response['meta']['lastPage'];
            $page++;
        }

        if (empty($allSplats)) {
            throw new \Exception('No splats found from API');
        }

        // Clear existing data
        $this->clearAllSplats();

        // Store new data
        $count = 0;
        foreach ($allSplats as $splatData) {
            try {
                $this->storeSplat($splatData);
                $count++;
            } catch (\Exception $e) {
                $this->logger->error('Error storing splat: ' . $e->getMessage(), ['splat_data' => $splatData]);
            }
        }

        return $count;
    }

    /**
     * Store splat data
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    protected function storeSplat(array $data)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('greenview_splats');

        $insertData = [
            'id' => $data['id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'slug' => $data['slug'] ?? '',
            'file_type' => $data['fileType'] ?? 'splat',
            'file_url' => $data['fileUrl'] ?? '',
            'file_ar_url' => $data['fileARUrl'] ?? '',
            'thumbnail_url' => $data['thumbnailUrl'] ?? '',
            'ar_short_link' => $data['arShortLink'] ?? '',
            'viewer_short_link' => $data['viewerShortLink'] ?? '',
            'ar_enabled' => isset($data['arEnabled']) ? (int)$data['arEnabled'] : 1,
            'created_at' => $data['createdAt'] ?? null,
            'updated_at' => $data['updatedAt'] ?? null,
            'plugin_config' => isset($data['pluginConfig']) ? json_encode($data['pluginConfig']) : null,
            'data_json' => json_encode($data)
        ];

        $connection->insertOnDuplicate($tableName, $insertData);
    }

    /**
     * Clear all splats
     *
     * @return void
     */
    protected function clearAllSplats()
    {
        $connection = $this->splatResource->getConnection();
        $tableName = $this->splatResource->getMainTable();
        $connection->truncateTable($tableName);
    }

    /**
     * Get splat by slug from local database
     *
     * @param string $slug
     * @return \GreenView\Viewer\Model\Splat|null
     */
    public function getSplatBySlug($slug)
    {
        /** @var \GreenView\Viewer\Model\Splat $splat */
        $splat = $this->splatFactory->create();
        $this->splatResource->load($splat, $slug, 'slug');

        return $splat->getId() ? $splat : null;
    }

    /**
     * Get splat by ID from local database
     *
     * @param string $id
     * @return \GreenView\Viewer\Model\Splat|null
     */
    public function getSplatById($id)
    {
        /** @var \GreenView\Viewer\Model\Splat $splat */
        $splat = $this->splatFactory->create();
        $this->splatResource->load($splat, $id, 'id');

        return $splat->getId() ? $splat : null;
    }

    /**
     * Get splat data (tries local first, then API)
     *
     * @param string $identifier
     * @param bool $byId
     * @return array|null
     */
    public function getSplatData($identifier, $byId = false)
    {
        // Try local database first
        $splat = $byId ? $this->getSplatById($identifier) : $this->getSplatBySlug($identifier);

        if ($splat) {
            return $splat->getDataJsonArray();
        }

        // Fall back to API
        $apiData = $byId
            ? $this->apiClient->getSplatById($identifier)
            : $this->apiClient->getSplatBySlug($identifier);

        return $apiData;
    }
}
