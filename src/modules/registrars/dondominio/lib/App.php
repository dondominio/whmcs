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
    const VERSION = '1.0';

    protected $params = [];
    protected $services = [];

    public function __construct(array $params = [])
    {
        $this->params = [
            'apiuser' => array_key_exists('apiuser', $params) ? $params['apiuser'] : '',
            'apipasswd' => array_key_exists('apipasswd', $params) ? $params['apipasswd'] : ''
        ];
    }

    public static function getDir()
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
    }

    public function getServices()
    {
        return $this->services;
    }

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
                            'PluginForWHMCS' => static::VERSION,
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

    public function setWHMCSService(WHMCSService_Interface $service)
    {
        $this->services['whmcs'] = $service;
    }

    public function setAPIService(APIService_Interface $service)
    {
        $this->services['api'] = $service;
    }

    public function setImportService(ImportService_Interface $service)
    {
        $this->services['import'] = $service;
    }

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