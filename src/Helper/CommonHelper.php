<?php

namespace DropboxDigitalAssetsIntegrationBundle\Helper;

use Pimcore\Tool\Admin;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CommonHelper
{
    const pathToLocalFile = 'var/assets/';

    /**
     * get current user id
     * 
     * @return int
     */
    public static function getCurrentUserId(): int
    {
        $currentUser = Admin::getCurrentUser();

        return $currentUser->getId();
    }

    /**
     * get all the file paths including subfolder files
     * 
     * @return array
     */
    public static function getAllFilePaths(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::pathToLocalFile, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        $allFilepaths = [];

        foreach ($iterator as $path => $dir) {

            if (!$dir->isDir()) {
                $allFilepaths[] = $path;
            }
        }

        return $allFilepaths;
    }

    /**
     * format asset file name
     * 
     * @param string $filePath
     * @return string
     */
    public static function formatAssetName(string $fiilePath): string
    {
        return str_replace(self::pathToLocalFile, '', $fiilePath);
    }
}
