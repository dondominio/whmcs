<?php

namespace WHMCS\Module\Registrar\Dondominio;

use WHMCS\Module\Registrar\Dondominio\Services\Contracts\WHMCSService_Interface;
use WHMCS\Module\Registrar\Dondominio\Services\Contracts\APIService_Interface;
use WHMCS\Module\Registrar\Dondominio\Services\Contracts\ImportService_Interface;
use WHMCS\Module\Registrar\Dondominio\Services\WHMCS_Service;
use WHMCS\Module\Registrar\Dondominio\Services\API_Service;
use WHMCS\Module\Registrar\Dondominio\Services\Import_Service;
use Exception;

class App
{
    const NAME = 'dondominio';
    const UNKNOWN_VERSION = 'unknown';

    protected $version;
    protected $params = [];
    protected $services = [];

    /**
     * Construct
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = [
            'apiuser' => array_key_exists('apiuser', $params) ? $params['apiuser'] : '',
            'apipasswd' => array_key_exists('apipasswd', $params) ? $params['apipasswd'] : ''
        ];
    }

    /**
     * Get App Name
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Retrieves App directory
     *
     * @return string
     */
    public static function getDir()
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
    }

    /**
     * Returns version.json file path
     *
     * @return string
     */
    public static function getVersionFilePath()
    {
        return static::getDir() . DIRECTORY_SEPARATOR . 'version.json';
    }

    /**
     * Returns App's version
     *
     * @return string
     */
    public function getVersion()
    {
        if (is_null($this->version)) {
            try {
                $versionFile = static::getVersionFilePath();

                if (!file_exists($versionFile)) {
                    throw new Exception('Version File not exists.');
                }

                $json = @file_get_contents($versionFile);

                if (empty($json)) {
                    throw new Exception('Version json file is empty.');
                }

                $versionInfo = json_decode($json, true);

                if (!is_array($versionInfo) || !array_key_exists('version', $versionInfo)) {
                    throw new Exception('Version index not found in version json file.');
                }

                $this->version = $versionInfo['version'];
            } catch (Exception $e) {
                $this->version = static::UNKNOWN_VERSION;
            }
        }

        return $this->version;
    }

    /**
     * Retrieves Services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Retrieve a Service by key
     *
     * @throws Exception If service not found
     *
     * @return mixed
     */
    public function getService($key)
    {
        if (!array_key_exists($key, $this->getServices())) {
            switch ($key) {
                case 'whmcs';
                    $this->setWHMCSService(new WHMCS_Service($this));
                break;
                case 'api';
                    $apiService = new API_Service([
                        'apiuser' => $this->params['apiuser'],
                        'apipasswd' => $this->params['apipasswd'],
                        'userAgent' => [
                            'PluginForWHMCS' => $this->getVersion(),
                            'WHMCS' => $this->getService('whmcs')->getConfiguration('version')
                        ]
                    ], $this);
                    $this->setAPIService($apiService);
                break;
                case 'import';
                    $this->setImportService(new Import_Service([], $this));
                break;
                default:
                    throw new Exception('[Fatal error] Service ' . $key . ' doesnt exists.');
                break;
            }
        }

        return $this->services[$key];
    }

    /**
     * Setter of WHMCS Service
     *
     * @param WHMCS\Module\Registrar\Dondominio\Services\Contracts\WHMCSService_Interface $service
     *
     * @return void
     */
    public function setWHMCSService(WHMCSService_Interface $service)
    {
        $this->services['whmcs'] = $service;
    }

    /**
     * Setter of API Service
     *
     * @param WHMCS\Module\Registrar\Dondominio\Services\Contracts\APIService_Interface $service
     *
     * @return void
     */
    public function setAPIService(APIService_Interface $service)
    {
        $this->services['api'] = $service;
    }

    /**
     * Setter of Import Service
     *
     * @param WHMCS\Module\Registrar\Dondominio\Services\Contracts\ImportService_Interface $service
     *
     * @return void
     */
    public function setImportService(ImportService_Interface $service)
    {
        $this->services['import'] = $service;
    }

    /**
     * Given an action, it executes it
     *
     * @param string $action Action to execute
     * @param array $params Params to pass to action
     *
     * @return mixed
     */
    public function dispatchAction($action, array $params = [])
    {
        if (strpos($action, '_') !== false) {
            $action = substr(strrchr($action, "_"), 1);
        }

        $className = '\WHMCS\Module\Registrar\Dondominio\Actions\\' . $action;

        if (!class_exists($className)) {
            throw new Exception('Class ' . $className . ' doesnt exists.');
        }

        if (!method_exists($className, '__invoke')) {
            throw new Exception('Action ' . $action . ' doesnt implements __invoke method.');
        }

        $obj = new $className($this, $params);
        return $obj();
    }
}