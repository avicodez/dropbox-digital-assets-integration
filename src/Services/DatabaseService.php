<?php

namespace DropboxDigitalAssetsIntegrationBundle\Services;

use Pimcore\Db;

class DatabaseService
{
    
    /**
     * Get file host config data of the current user
     * 
     * @param int $userId
     * 
     * @return array
     */
    public function getConfigData(int $userId) : array
    {
        $configData = Db::get()->fetchRow("SELECT * FROM `plugin_twohats_dropbox_data` WHERE `user_id` = " . $userId);

        return $configData ? $configData : [];
    }

    /**
     * Save file host configuration data to DB
     * 
     * @param array $postData
     * @param string $host
     * @param int $userId
     * 
     * @return void  
     */
    public function saveConfigData(array $postData, string $host, int $userId) : void
    {
        Db::get()->query(
            sprintf('replace into plugin_twohats_dropbox_data (user_id, host, client_id, client_secret, client_access_token) VALUES (%d, "%s", "%s", "%s", "%s")', 
            $userId, $host, $postData['dropbox_client_id'], $postData['dropbox_client_secret'], $postData['dropbox_acess_token'])
        );
    }
}