<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Home_Controller extends Controller
{
    const CONTROLLER_NAME = '';
    const DEFAULT_TEMPLATE_FOLDER = 'home';

    const VIEW_INDEX = '';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
        ];
    }

    /**
     * View for home
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        return $this->view('index', [
            'title' => 'DonDominio',
        ]);
    }
    
}