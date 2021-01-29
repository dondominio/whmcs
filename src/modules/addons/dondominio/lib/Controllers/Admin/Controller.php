<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\Controllers\Controller as BaseController;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class Controller extends BaseController
{
    const VIEW_INDEX = '';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index'
        ];
    }
}