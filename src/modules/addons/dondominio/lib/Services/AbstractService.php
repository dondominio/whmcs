<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\App;

abstract class AbstractService
{
    /**
     * Returns App Instance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\App
     */
    public function getApp()
    {
        return App::getInstance();
    }
}