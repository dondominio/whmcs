<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\UtilsService_Interface;

class Utils_Service extends AbstractService implements UtilsService_Interface
{
    public function addonIsOutdated()
    {
        $localVersion = $this->getApp()->getVersion();

        if ($localVersion == App::UNKNOWN_VERSION) {
            return false;
        }

        if (empty($localVersion)) {
            return false;
        }

        $githubVersionInfo = file_get_contents('https://raw.githubusercontent.com/dondominio/whmcs-addon/master/version.json');

        // Have we retrieved anything?
        if (empty($githubVersionInfo)) {
            return false;
        }

        $githubJson = json_decode($githubVersionInfo, true);

        // Have we decoded the JSONs correctly?
        if (!is_array($githubJson)) {
            return false;
        }

        // Comparing the versions found on the JSONs
        if (version_compare($localVersion, $githubJson['version']) < 0) {
            return true;
        }

        return false;
    }

    public function pluginIsOutdated()
    {
        $pluginDir = implode(DIRECTORY_SEPARATOR, [$this->getApp()->getDir(), '..', '..', 'registrars', 'dondominio', 'version.json']);

        if(!is_dir($pluginDir)) {
            return false;
        }

        $localVersionInfo = @file_get_contents($pluginDir . DIRECTORY_SEPARATOR . 'version.json');
        $githubVersionInfo = @file_get_contents('https://raw.githubusercontent.com/dondominio/whmcs-plugin/master/version.json');

        // Have we retrieved anything?
        if (empty($localVersionInfo) || empty($githubVersionInfo)) {
            return false;
        }

        $localJson = json_decode($localVersionInfo, true);
        $githubJson = json_decode($githubVersionInfo, true);

        // Have we decoded the JSONs correctly?
        if (!is_array($localJson) || !is_array($githubJson)) {
            return false;
        }

        // Comparing versions found on the JSONs
        if (version_compare($localJson['version'], $githubJson['version']) < 0) {
            return $githubJson;
        }

        return false;
    }
}