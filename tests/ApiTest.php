<?php

namespace Dondominio\WHMCS\Tests;

use PHPUnit\Framework\TestCase;
use WHMCS\Module\Registrar\Dondominio\App as RegistrarApp;
use WHMCS\Module\Addon\Dondominio\Services\API_Service as AddonApiService;
use WHMCS\Module\Registrar\Dondominio\Services\API_Service as RegistrarApiService;

class ApiTest extends TestCase
{
    /**
     * Get API Data
     */
    protected function getApiData()
    {
        return [
            'apiuser' => 'a',
            'apipasswd' => 'a',
            'userAgent' => ['TestCase' => true]
        ];
    }

    /**
     * Get Addon API Service
     */
    protected function getAddonApiService()
    {
        return new AddonApiService($this->getApiData());
    }

    /**
     * Get Registrar API Service
     */
    protected function getRegistrarApiService()
    {
        $app = new RegistrarApp();
        return new RegistrarApiService($this->getApiData(), $app);
    }

    public function testLoadApis()
    {
        $this->assertTrue(is_object($this->getAddonApiService()->getApiConnection()));
        $this->assertTrue(is_object($this->getRegistrarApiService()->getApiConnection()));
    }
}