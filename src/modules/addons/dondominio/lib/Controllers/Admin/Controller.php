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

    /**
     * Searchs and returns a template
     * 
     * @param string $view View in format "folder.file" or "file"
     * @param array $params Params to pass to template
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view($view, array $params = [])
    {
        $params['css_path'] = sprintf('/modules/addons/%s/css/', $this->getApp()->getName());

        if(!isset($params['print_nav'])){
            $params['print_nav'] = true;
        }
        
        return parent::view($view, $params);
    }
}

