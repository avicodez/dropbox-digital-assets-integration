<?php

namespace DropboxDigitalAssetsIntegrationBundle\Services;

interface FileHost
{
    public function setConnection($configData);
    public function multipleFilesUpload($host, $chunkedFiles);
    public function uploadFile($host, $fileSource, $fileName);
}
