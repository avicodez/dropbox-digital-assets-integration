<?php

namespace DropboxDigitalAssetsIntegrationBundle\Tools;

use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;

class Installer extends SettingsStoreAwareInstaller
{
    public function install()
    {
        $this->installDatabaseTables();
        parent::install();

        return true;
    }


    public function installDatabaseTables()
    {
        Db::get()->query(
            'CREATE TABLE IF NOT EXISTS `plugin_twohats_dropbox_data` (
              `user_id` int(11) unsigned NOT NULL,
              `host` ENUM("dropbox") NOT NULL,
              `client_id` varchar(255),
              `client_secret` varchar(255),
              `client_access_token` varchar(255),
              PRIMARY KEY (`user_id`,`host`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        parent::install();
    }

    public function uninstall()
    {
        $db = Db::get();
        $db->query('DROP TABLE IF EXISTS `plugin_twohats_dropbox_data`');
        parent::uninstall();
    }
}