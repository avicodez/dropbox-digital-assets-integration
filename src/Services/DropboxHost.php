<?php

namespace DropboxDigitalAssetsIntegrationBundle\Services;

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use DropboxDigitalAssetsIntegrationBundle\Helper\CommonHelper;
use Psr\Log\LoggerInterface;

class DropboxHost implements FileHost
{
    
    /** 
     * DropboxHost Constructor
     * 
     * @param LoggerInterface $logger 
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Set connection with the DropBox
     * 
     * @param array $configData
     * 
     * @return object 
     */
    public function setConnection($configData) : object
    {
        $app = new DropboxApp($configData['client_id'], $configData['client_secret'], $configData['client_access_token']);
        $dropbox = new Dropbox($app);

        return $dropbox;
    }

    /**
     * Upload multiple files to the Dropbox
     * 
     * @param object $host
     * @param array $chunkedFiles
     * 
     * @return array
     */
    public function multipleFilesUpload($host, $chunkedFiles) : array
    {
        try {
            foreach ($chunkedFiles as $filePathArray) {
                $this->bulkUpdate($filePathArray, $host);
            }

            return [
                'data' => 
                    [
                        'status' => 'success', 
                        'message' => 'Assets uploaded to Dropbox successfully'
                    ], 
                'status' => 200];
        } catch(DropboxClientException $e) {
            $this->logger->error($e->getMessage());
            
            return [
                'data' => 
                    [
                        'status' => 'error', 
                        'message' => $this->customDropBoxExceptionMsgs($e)
                    ], 
                'status' => 500];
        }
    }

    /**
     * Bulk update to the DropBox
     * 
     * @param array $filePathArray
     * @param object $dropBox
     * 
     * @return void 
     */
    private function bulkUpdate($filePathArray, $dropBox) : void
    {
        foreach ($filePathArray as $singleFilePath) {
            $fileName = CommonHelper::formatAssetName($singleFilePath);
            $this->uploadFile($dropBox, $singleFilePath, $fileName);
            
        }
    }

    /**
     * Upload single file to dropbox
     * 
     * @param object $host
     * @param string $fileSource
     * @param string $fileName
     * 
     * @return void
     */
    public function uploadFile($host, $fileSource, $fileName)
    {
        $host->upload($fileSource, '/assets/' . $fileName, ['autorename' => true]);
    }

    /**
     * format custom message for Dropbox exceptions
     * 
     * @param object $exception
     * 
     * @return string
     */
    private function customDropBoxExceptionMsgs($exception)
    {
        $exceptionText = json_decode($exception->getMessage(), true);

        if($exception->getCode() == 401) {
            $msg = $exceptionText['error']['.tag'];
            $msg = ucwords(str_replace("_", " ", $msg));
        } else {
            $msg = 'Invalid Access Token';
        }

        return $msg;
    }
}
