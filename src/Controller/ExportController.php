<?php

namespace DropboxDigitalAssetsIntegrationBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Exception;
use DropboxDigitalAssetsIntegrationBundle\Services\DatabaseService;
use DropboxDigitalAssetsIntegrationBundle\Services\FileHost;
use DropboxDigitalAssetsIntegrationBundle\Helper\CommonHelper;

class ExportController extends FrontendController
{
    private $logger;
    private $fileHost;
    private $dbService;
    
    /**
     * Constructor For Export Controller
     * 
     * @param LoggerInterface $logger
     * 
     * @param FileHost $logger
     */
    public function __construct(LoggerInterface $logger, FileHost $fileHost, DatabaseService $dbService)
    {
        $this->logger = $logger;
        $this->fileHost = $fileHost;
        $this->dbService = $dbService;
    }

    /**
     * Displaying the form for host configurations
     * 
     * @Route("admin/dropbox_digital_assets_integration/settings")
     * 
     * @return Response
     */
    public function settingsAction()
    {
        $configData = $this->getConfigData();

        return $this->render('@DropboxDigitalAssetsIntegration/export/settings.html.twig', [
            'configData' => $configData 
        ]);
    }

    /**
     * Get config data of the current user
     * 
     * @return array
     */
    private function getConfigData()
    {
        $userId = CommonHelper::getCurrentUserId();
        
        return $this->dbService->getConfigData($userId);
    }

    /**
     * Store the configuration data to the DB
     * 
     * @Route("admin/dropbox_digital_assets_integration/settings/submit", methods="POST")
     * 
     * @param Request $request
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function settingsSubmitAction(Request $request)
    {
        
        try {
            $userId = CommonHelper::getCurrentUserId();
            $host = 'dropbox';
            $this->dbService->saveConfigData($request->request->all(), $host, $userId);
            
            return $this->json(['status' => 'success', 'message' => 'Configurations added successfully'], 200);
        } catch(Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json(['status' => 'error', 'message' => 'Operation Failed'], 500);
        }
    }

    /**
     * Sync the didgital assets to the file host
     * 
     * @Route("admin/dropbox_digital_assets_integration/sync/submit")
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function syncSubmitAction()
    {
        $configData = $this->getConfigData();
        
        if (empty($configData)) {
            return $this->json(['status' => 'error', 'message' => "Invalid Credentials"]);
        }
        
        try {
            $allFilepaths = CommonHelper::getAllFilePaths();
            $filePathChunks = array_chunk($allFilepaths, 10);
            $fileUploadResponse = $this->chunkFilesUpload($filePathChunks, $configData);

            return $this->json($fileUploadResponse['data'], $fileUploadResponse['status']);
        } catch(Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Uploaded the chunked arrays of files to file host 
     * 
     * @param array $filePathChunks  
     * @param array $configData
     * 
     * @return array  
     */
    private function chunkFilesUpload($filePathChunks, $configData)
    {
        $dropbox = $this->fileHost->setConnection($configData);
        
        return $this->fileHost->multipleFilesUpload($dropbox, $filePathChunks);
    }
}
