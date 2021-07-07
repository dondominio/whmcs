<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\UtilsService_Interface;
use WHMCS\Module\Registrar;
use Exception;
use DateTime;

class Utils_Service extends AbstractService implements UtilsService_Interface
{
    const UPDATE_VERSION_HOUR_INTERVAL = 6;

    /**
     * Retrieves latest version number from github
     *
     * @return string in format "x.y.z"
     */
    public function getLatestVersion()
    {
        $json = file_get_contents('https://raw.githubusercontent.com/dondominio/whmcs/main/src/modules/addons/dondominio/version.json');

        // Have we retrieved anything?
        if (empty($json)) {
            throw new Exception('unable_to_retrieve_last_version');
        }

        $info = json_decode($json, true);

        // Have we decoded the JSONs correctly?
        if (!is_array($info)) {
            throw new Exception('latest_version_decoded_unsuccessfully');
        }

        return $info['version'];
    }

    /**
     * Retrieves if version installed is the latest
     * 
     * @throws Exception if unable to retrieve local version
     *
     * @return bool
     */
    public function isLatestVersion()
    {
        $localVersion = $this->getApp()->getVersion();
        $settingsService = $this->getApp()->getService('settings');

        if ($localVersion == App::UNKNOWN_VERSION) {
            throw new Exception('unable_to_retrieve_local_version');
        }

        if (empty($localVersion)) {
            throw new Exception('local_version_is_empty');
        }

        $versionUpdate = $settingsService->getTsSetting('last_version_ts_update');

        $versionUpdate->modify(sprintf('+%d hours', static::UPDATE_VERSION_HOUR_INTERVAL));
        $now = new DateTime();

        if ($versionUpdate < $now){
            try {
                $latestVersion = $this->getLatestVersion();
                $settingsService->setSetting('last_version', $latestVersion);
            } catch (\Exception $e){}

            $settingsService->setSetting('last_version_ts_update', new \DateTime());
        }

        $latestVersion = $settingsService->getSetting('last_version');

        // Comparing the versions found on the JSONs
        return version_compare($localVersion, $latestVersion, '>=');
    }

    /**
     * Find if registrar module is installed
     *
     * @return bool
     */
    public function findRegistrarModule()
    {
        $registrarPath = static::buildPath([ROOTDIR, 'modules', 'registrars', 'dondominio']);

        if (!is_dir($registrarPath)) {
            throw new Exception('registrar_folder_not_found');
        }

        $registrarFile = static::buildPath([$registrarPath, 'dondominio.php']);

        if (!file_exists($registrarFile)) {
            throw new Exception('registrar_file_not_found');
        }

        return true;
    }

    /**
     * Update Modules
     *
     * Downloads the newest version from github and installs it
     *
     * @return bool
     */
    public function updateModules()
    {
        $method = null;
        $extensions = ['zip', 'Phar'];

        foreach ($extensions as $extension) {
            if (extension_loaded($extension)) {
                $method = $extension;
                break;
            }
        }

        if (empty($method)) {
            throw new Exception('no_extension_found_for_decompressing');
        }

        // DOWNLOAD
        $tarballPath = $this->downloadLatestVersion($method);

        // DECOMPRESS
        $latestVersionPath = $this->decompressFile($tarballPath, $method);

        // INSTALL MODULES (MOVE TO DESTINATION)
        $this->installModules($latestVersionPath);

    }

    /**
     * Downloads latest version and returns path
     *
     * @throws Exception if unable to download
     *
     * @return string
     */
    protected function downloadLatestVersion($method)
    {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
                ]
            ]
        ];
        $context = stream_context_create($opts);

        $latestInfo = @file_get_contents('https://api.github.com/repos/dondominio/whmcs/releases/latest', false, $context);
        $jsonLatestInfo = json_decode($latestInfo, true);

        if (empty($jsonLatestInfo)) {
            throw new Exception('unable_to_retrieve_latest_json_info');
        }

        // DOWNLOAD LATEST VERSION (tarball)

        $prefix = $method == 'zip' ? 'zip' : 'tar';
        $tarballContents = @file_get_contents($jsonLatestInfo[$prefix . 'ball_url'], false, $context);

        if (empty($tarballContents)) {
            throw new Exception('unable_to_download_latest_version');
        }

        // SAVE DOWNLOAD

        $extension = $method == 'zip' ? 'zip' : 'tar.gz';
        $downloadPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'latest-version.' . $extension;

        if (!file_put_contents($downloadPath, $tarballContents)) {
            throw new Exception('couldnt_save_download');
        }

        return $downloadPath;
    }

    /**
     * Decompress a file and return path of the folder
     *
     * @throws Exception if extract failed
     *
     * @return string
     */
    protected function decompressFile($downloadPath, $method)
    {
        // Get name from /tmp
        $folder = tempnam(sys_get_temp_dir(), '');
        unlink($folder);

        // Extract into /tmp
        switch (strtolower($method)) {
            case 'zip':
                $zip = new \ZipArchive();

                if ($zip->open($downloadPath) !== true) {
                    throw new Exception('unable_to_open_compressed_file');
                }

                // $zip->getStatusString() to check errors

                if (!$zip->extractTo($folder)) {
                    throw new Exception('unable_to_decompress');
                }

                $zip->close();
            break;

            case 'phar':
                // ERROR: RecursiveDirectoryIterator::__construct(): Unable to find the wrapper "phar" - did you forget to enable it when you configured PHP?
                // Solution found in https://github.com/mageplaza/magento-2-geoip-library/issues/3
                @stream_wrapper_restore('phar');

                $phar = new \PharData($downloadPath);

                $extracted = $phar->extractTo($folder);

                @stream_wrapper_unregister('phar');

                if (!$extracted) {
                    throw new Exception('unable_to_decompress');
                }
            break;

            default:
                throw new Exception('decompress_method_not_implemented');
            break;
        }

        $rdi = new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS);
        $element = $rdi->current();

        if (is_null($element)) {
            throw new Exception('unable_to_decompress');
        }

        return $element->getPathName() . DIRECTORY_SEPARATOR . 'src';
    }

   /**
     * Moves files to specified directories to install the modules
     *
     * @param string $downloadFolder Path where the installation folder is
     *
     * @throws Exception if modules update failed
     *
     * @return void
     */
    protected function installModules(string $downloadFolder): void
    {
        $modulesFoldersPath = [];
        $baseFoldersPath = [
            static::buildPath(['includes', 'dondominio']),
            static::buildPath(['modules', 'registrars']),
            static::buildPath(['modules', 'addons']),
            static::buildPath(['modules', 'servers']),
            static::buildPath(['modules', 'gateways']),
            static::buildPath(['modules', 'mail']),
            static::buildPath(['modules', 'notifications']),
        ];

        foreach ($baseFoldersPath as $path){
            $realPath = static::buildPath([$downloadFolder, $path]);
            $subFolders = scandir($realPath);

            foreach ($subFolders as $subFolder){
                $realSubPath = static::buildPath([$downloadFolder, $subFolder]);

                if (!in_array($subFolder, ['.', '..']) && !is_dir($realSubPath)){
                    $modulesFoldersPath[] = static::buildPath([$path, $subFolder]);
                }
            }
        }

        $this->checkPermissions($modulesFoldersPath, $downloadFolder);

        $backupsReference = uniqid() . '0';
        $downloadsReference = uniqid() . '1';

        try {
            $this->checkBaseFolders($baseFoldersPath);
            $this->doBackups($modulesFoldersPath, $backupsReference);
            $this->moveDownloads($modulesFoldersPath, $downloadFolder, $downloadsReference);
            $this->replaceModules($modulesFoldersPath, $downloadsReference);
            $this->deleteDirectories($modulesFoldersPath, $backupsReference);
        } catch (\Throwable $e) {
            $this->deleteDirectories($modulesFoldersPath, $downloadsReference);
            $this->recoveryBackups($modulesFoldersPath, $backupsReference);
            throw $e;
        }
    }

    /**
     * Create includes & modules 
     *
     * @param array $baseFoldersPath Base folders for includes & modules
     *
     * @return void
     */
    protected function checkBaseFolders(array $baseFoldersPath): void
    {
        foreach ($baseFoldersPath as $folder){
            $realPath = static::buildPath([ROOTDIR, $folder]);

            if (!file_exists($realPath)) {
                mkdir($realPath, 0755, true);
            }
        }
    }

    /**
     * Moves directories from downloadFolder to temporal destination inside WHMCS
     *
     * @param array $modulesFoldersPath relative paths to move
     * @param string $downloadFolder Path of source
     * @param string $rnd Random string for reference
     *
     * @throws Exception If rename went bad
     *
     * @return void
     */
    protected function moveDownloads($modulesFoldersPath, $downloadFolder, $rnd)
    {
        foreach ($modulesFoldersPath as $path) {
            $source = static::buildPath([$downloadFolder, $path]);
            $destination = static::buildPath([ROOTDIR, $path . '_' . $rnd]);

            if (!rename($source, $destination)) {
                $error = error_get_last();
                throw new Exception(
                    sprintf('Error while copying %s into %s: %s', $source, $destination, $error['message'])
                );
            }
        }
    }

    /**
     * Makes backup of original content
     *
     * @param array $modulesFoldersPath relative paths to move
     * @param string $rnd Random string for reference
     *
     * @throws Exception If backup creation went bad
     *
     * @return void
     */
    protected function doBackups($modulesFoldersPath, $rnd)
    {
        foreach ($modulesFoldersPath as $path) {
            $source = static::buildPath([ROOTDIR, $path]);
            $destination = static::buildPath([ROOTDIR, $path . '_' . $rnd]);

            if (!file_exists($source) && !is_link($source)) {
                continue;
            }

            if (!rename($source, $destination)) {
                $error = error_get_last();
                throw new Exception(
                    sprintf('Error while doing backup of %s: %s', $source, $error['message'])
                );
            }
        }
    }

    /**
     * Deletes modules and replaces it with the downloaded ones
     * Before this function, we need to make sure about priviliges calling $this->checkPermissions()
     *
     * @param array $modulesFoldersPath relative paths to move
     * @param string $rnd Random string for reference
     *
     * @throws Exception if deletion or rename went bad
     *
     * @return void
     */
    protected function replaceModules($modulesFoldersPath, $rnd)
    {
        foreach ($modulesFoldersPath as $path) {
            $source = static::buildPath([ROOTDIR, $path . '_' . $rnd]);
            $destination = static::buildPath([ROOTDIR, $path]);

            if (!rename($source, $destination)) {
                $error = error_get_last();
                throw new Exception(
                    sprintf('Error while copying %s into %s: %s. You MUST download the modules and copy them manually in WHMCS root folder.',
                    $source, $destination, $error['message'])
                );
            }
        }
    }

    /**
     * Removes temporary folders
     *
     * @param array $modulesFoldersPath relative paths to move
     * @param string $rnd Random string for reference
     *
     * @return void
     */
    protected function deleteDirectories($modulesFoldersPath, $rnd)
    {
         foreach ($modulesFoldersPath as $path) {
            $directory = static::buildPath([ROOTDIR, $path . '_' . $rnd]);
            $this->deleteDirectory($directory);
        }
    }

    /**
     * Deletes installed content and recoveries old content
     *
     * @param array $modulesFoldersPath relative paths to move
     * @param string $rnd Random string for reference
     *
     * @return void
     */
    protected function recoveryBackups($modulesFoldersPath, $rnd)
    {
        foreach ($modulesFoldersPath as $path) {
            $source = static::buildPath([ROOTDIR, $path . '_' . $rnd]);
            $destination = static::buildPath([ROOTDIR, $path]);

            // If backup doesn't exist and is required
            // we already threw Exception in $this->doBackup()
            if (!file_exists($source) && !is_link($source)) {
                continue;
            }

            if (!$this->deleteDirectory($destination)) {
                $error = error_get_last();
                throw new Exception(
                    sprintf('Error while deleting %s: %s. You MUST download the modules and copy them manually in WHMCS root folder.',
                    $destination, $error['message'])
                );
            }

            if (!rename($source, $destination)) {
                $error = error_get_last();
                throw new Exception(
                    sprintf('Error while copying %s into %s: %s. You MUST download the modules and copy them manually in WHMCS root folder.',
                    $source, $destination, $error['message'])
                );
            }
        }
    }

    /**
     * Check permissions on files and directories
     * Final destination directory could not exist and that's ok
     *
     * @param array $modulesFoldersPath relative paths to check
     * @param string $downloadFolder Folder where we downloaded new version
     *
     * @throws \Exception If permission fails
     *
     * @return void
     */
    protected function checkPermissions($modulesFoldersPath, $downloadFolder)
    {
        // Check modules parents
        foreach ($modulesFoldersPath as $path) {
            $sourceParent = dirname(static::buildPath([$downloadFolder, $path]));
            $destinationParent = dirname(static::buildPath([ROOTDIR, $path]));

            $this->hasPermission($sourceParent, false);
            $this->hasPermission($destinationParent, false);
        }

        // Check permissions inside modules
        foreach ($modulesFoldersPath as $path) {
            $source = static::buildPath([$downloadFolder, $path]);
            $this->hasPermission($source, true);

            $destination = static::buildPath([ROOTDIR, $path]);

            if (file_exists($destination)) {
                $this->hasPermission($destination, true);
            }
        }
    }

    /**
     * Evaluates permission on path
     *
     * @param string $path Path to evaluate
     * @param bool $recursive if evaluation is recursive
     *
     * @throws Exception If no permission
     *
     * @return bool
     */
    protected function hasPermission($path, $recursive = false)
    {
        if (is_link($path)) {
            return true;
        }

        if (!file_exists($path)) {
            throw new Exception(sprintf('Error: %s not exists.', $path));
        }

        if (!is_writable($path)) {
            throw new \Exception(sprintf('Error: no write permission in %s. You need to enable write permission.', $path));
        }

        if (!is_readable($path)) {
            throw new \Exception(sprintf('Error: no read permission in %s. You need to enable read permission.', $path));
        }

        if (!is_dir($path) || !$recursive) {
            return true;
        }

        if (!is_dir($path)) {
            throw new Exception(sprintf('Error: %s is not a directory.', $path));
        }

        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->hasPermission($path . DIRECTORY_SEPARATOR . $item, $recursive)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes a directory and its contents
     *
     * @param string Path of directory
     *
     * @return bool
     */
    protected function deleteDirectory($dir)
    {
        if (!is_link($dir) && !file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Retrieves if Registrar Module is active
     * 
     * @return bool
     */
    public function isRegistrarModuleActive()
    {
        $registrar = new Registrar();

        return $registrar->isActive('dondominio');
    }

    /**
     * Path builder
     *
     * @param array $arr Elements to glue
     * @param bool $is_dir If we are building directory
     *
     * @return string
     */
    public static function buildPath($arr)
    {
        return implode(DIRECTORY_SEPARATOR, $arr);
    }

    /**
     * Returns WHMCS version
     *
     * @return string
     */
    public function getWHMCSVersion()
    {
        if(isset($GLOBALS['CONFIG']['Version'])){
            return $GLOBALS['CONFIG']['Version'];
        }

        return '';
    }

}