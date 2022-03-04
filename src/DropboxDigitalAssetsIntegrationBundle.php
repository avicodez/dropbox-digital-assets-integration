<?php

namespace DropboxDigitalAssetsIntegrationBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use DropboxDigitalAssetsIntegrationBundle\Tools\Installer;

class DropboxDigitalAssetsIntegrationBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/dropboxdigitalassetsintegration/js/pimcore/startup.js'
        ];
    }

    public function getCssPaths()
    {
        return [
            '/bundles/dropboxdigitalassetsintegration/css/plugin.css'
        ];
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}