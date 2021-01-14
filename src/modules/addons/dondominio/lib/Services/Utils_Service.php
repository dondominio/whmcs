<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\UtilsService_Interface;
use Exception;

class Utils_Service extends AbstractService implements UtilsService_Interface
{
    public function getLatestVersion()
    {
        $json = file_get_contents('https://raw.githubusercontent.com/dondominio/whmcs/main/src/modules/addons/dondominio/version.json');

        // Have we retrieved anything?
        if (empty($json)) {
            throw new Exception('Unable to retrieve latest addon version.');
        }

        $info = json_decode($json, true);

        // Have we decoded the JSONs correctly?
        if (!is_array($info)) {
            throw new Exception('Retrieving latest version was unsuccessfully decoded.');
        }

        return $info['version'];
    }

    public function isLatestVersion()
    {
        $localVersion = $this->getApp()->getVersion();

        if ($localVersion == App::UNKNOWN_VERSION) {
            throw new Exception('Unable to retrieve local version.');
        }

        if (empty($localVersion)) {
            throw new Exception('Unable to retrieve local version. Version is empty.');
        }

        $latestVersion = $this->getLatestVersion();

        // Comparing the versions found on the JSONs
        return version_compare($localVersion, $latestVersion, '>=');
    }

    public function findRegistrarModule()
    {
        $registrarPath = implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'modules', 'registrars', 'dondominio']);

        if (!is_dir($registrarPath)) {
            throw new Exception(sprintf('Registrar folder (%s) not found.', $registrarPath));
        }

        $registrarFile = implode(DIRECTORY_SEPARATOR, [$registrarPath, 'dondominio.php']);

        if (!file_exists($registrarFile)) {
            throw new Exception(sprintf('Registrar file (%s) not found.', $registrarFile));
        }

        return true;
    }
}