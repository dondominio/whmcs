<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\UtilsService_Interface;
use Exception;

class Utils_Service extends AbstractService implements UtilsService_Interface
{
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

        if ($localVersion == App::UNKNOWN_VERSION) {
            throw new Exception('unable_to_retrieve_local_version');
        }

        if (empty($localVersion)) {
            throw new Exception('local_version_is_empty');
        }

        $latestVersion = $this->getLatestVersion();

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
        $registrarPath = implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'modules', 'registrars', 'dondominio']);

        if (!is_dir($registrarPath)) {
            throw new Exception('registrar_folder_not_found');
        }

        $registrarFile = implode(DIRECTORY_SEPARATOR, [$registrarPath, 'dondominio.php']);

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
                stream_wrapper_restore('phar');

                $phar = new \PharData($downloadPath);
                $extracted = $phar->extractTo($folder);

                stream_wrapper_unregister('phar');

                if (!$extracted) {
                    throw new Exception('unable_to_decompress');
                }
            break;

            default:
                throw new Exception('decompress_method_not_implemented');
            break;
        }

        // Return inside folder
        $folder .= DIRECTORY_SEPARATOR . 'whmcs-repo-init';

        return $folder;
    }

    /**
     * Moves files to specified directories to install the modules
     *
     * @throws Exception if module moving failed
     *
     * @return void
     */
    protected function installModules($folder)
    {
        $modulesFoldersPath = [
            implode(DIRECTORY_SEPARATOR, ['modules', 'addons', 'dondominio']),
            implode(DIRECTORY_SEPARATOR, ['modules', 'registrars', 'dondominio']),
            implode(DIRECTORY_SEPARATOR, ['includes', 'dondomino'])
        ];

        foreach ($modulesFoldersPath as $path) {
            $source = implode(DIRECTORY_SEPARATOR, [$folder, $path]);
            $destination = implode(DIRECTORY_SEPARATOR, [ROOTDIR, $path]);

            if (!is_dir($source) || !is_dir($destination)) {
                continue;
            }

            if (!rename($source, $destination)) {
                throw new Exception(
                    sprintf('Error while copying %s into %s', $source, $destination)
                );
            }
        }
    }
}