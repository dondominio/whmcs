<?php

namespace Dondominio\WHMCS\Tests;

use PHPUnit\Framework\TestCase;
use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Services\Whois_Service;

class AddonTest extends TestCase
{
    /**
     * Test App Services instantation
     */
    public function testInitializeServices()
    {
        $services = ['pricing', 'settings', 'watchlist', 'tld_settings', 'whmcs', 'whois', 'email', 'utils'];

        // WE CANNOT TEST API SERVICE SINCE IT HAS DEPENDENCIES UPON DATABASE (username and password)

        $app = App::getInstance();

        foreach ($services as $service) {
            $this->assertTrue(is_object($app->getService($service)));
        }
    }
}