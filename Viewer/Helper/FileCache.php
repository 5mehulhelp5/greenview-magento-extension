<?php
/**
 * File Cache Helper
 *
 * @category   GreenView
 * @package    GreenView_Viewer
 * @author     Indra Gunanda
 * @copyright  Copyright (c) 2024 GreenView
 */

namespace GreenView\Viewer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class FileCache extends AbstractHelper
{
    const CACHE_DIR = 'greenview-splats';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param Curl $curl
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        Curl $curl,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        Data $helper
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Get cached file URL
     *
     * @param array $splatData
     * @return string|null
     */
    public function getCachedFileUrl($splatData)
    {
        if (empty($splatData['fileUrl'])) {
            return null;
        }

        $fileExtension = $this->getFileExtension($splatData);
        $fileName = 'splat_' . $splatData['id'] . '.' . $fileExtension;

        $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $cachePath = self::CACHE_DIR . '/' . $fileName;
        $absolutePath = $mediaDir->getAbsolutePath($cachePath);

        // Check if cached and not expired
        if (file_exists($absolutePath)) {
            $cacheLifetime = $this->helper->getCacheLifetime();
            if ($cacheLifetime == 0 || (time() - filemtime($absolutePath) < $cacheLifetime)) {
                return $this->getCachedFilePublicUrl($fileName);
            }
        }

        // Download file
        try {
            if (!$mediaDir->isExist(self::CACHE_DIR)) {
                $mediaDir->create(self::CACHE_DIR);
            }

            $this->curl->setTimeout(60);
            $this->curl->get($splatData['fileUrl']);
            $fileContent = $this->curl->getBody();

            if ($fileContent) {
                $mediaDir->writeFile($cachePath, $fileContent);
                return $this->getCachedFilePublicUrl($fileName);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error caching splat file: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get file extension
     *
     * @param array $splatData
     * @return string
     */
    protected function getFileExtension($splatData)
    {
        // Try fileType field
        if (!empty($splatData['fileType'])) {
            return $splatData['fileType'];
        }

        // Try fileName
        if (!empty($splatData['fileName'])) {
            $pathInfo = pathinfo($splatData['fileName']);
            if (!empty($pathInfo['extension'])) {
                return $pathInfo['extension'];
            }
        }

        // Extract from URL
        if (!empty($splatData['fileUrl'])) {
            $urlPath = parse_url($splatData['fileUrl'], PHP_URL_PATH);
            $pathInfo = pathinfo($urlPath);
            if (!empty($pathInfo['extension'])) {
                return $pathInfo['extension'];
            }
        }

        return 'splat';
    }

    /**
     * Get cached file public URL
     *
     * @param string $fileName
     * @return string
     */
    protected function getCachedFilePublicUrl($fileName)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . self::CACHE_DIR . '/' . $fileName;
    }

    /**
     * Clear cache for specific splat
     *
     * @param string $splatId
     * @return void
     */
    public function clearCache($splatId = null)
    {
        $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($splatId) {
            $patterns = ['splat', 'ply', 'gltf', 'glb'];
            foreach ($patterns as $ext) {
                $fileName = 'splat_' . $splatId . '.' . $ext;
                $cachePath = self::CACHE_DIR . '/' . $fileName;
                if ($mediaDir->isExist($cachePath)) {
                    $mediaDir->delete($cachePath);
                }
            }
        } else {
            // Clear all
            if ($mediaDir->isExist(self::CACHE_DIR)) {
                $mediaDir->delete(self::CACHE_DIR);
            }
        }
    }
}
