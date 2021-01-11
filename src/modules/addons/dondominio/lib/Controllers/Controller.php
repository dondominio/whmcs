<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Helpers\Request;
use WHMCS\Module\Addon\Dondominio\Helpers\Response;
use WHMCS\Module\Addon\Dondominio\Helpers\Template;

abstract class Controller
{
    const CONTROLLER_NAME = '';
    const DEFAULT_TEMPLATE_FOLDER = '';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [];
    }

    /**
     * Returns Request instance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Request
     */
    public function getRequest()
    {
        return Request::getInstance();
    }

    /**
     * Returns Response instance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Response
     */
    public function getResponse()
    {
        return Response::getInstance();
    }

    /**
     * Returns App Instance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\App
     */
    public function getApp()
    {
        return App::getInstance();
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
        $parts = explode(".", $view);

        if (count($parts) == 2) {
            $folder = $parts[0];
            $file = $parts[1];
        } else {
            $folder = static::DEFAULT_TEMPLATE_FOLDER;
            $file = $parts[0];
        }

        $params = array_merge($params, [
            'LANG' => App::getInstance()->getLang(),
            'RESPONSE' => $this->getResponse()
        ]);

        return new Template("$folder/$file.tpl", $params);
    }

    /**
     * Makes URL for the controller
     * 
     * @param string $action Action
     * @param array $extraparams Extra Parameters (to http_query_builder)
     * 
     * @return string URL
     */
    public static function makeURL($action = '', array $extraparams = [])
    {
        $modulelink = App::getInstance()->getModuleLink();

        $params = ['__c__' => static::CONTROLLER_NAME];

        if (strlen($action) > 0) {
            $params['__a__'] = $action;
        }

        $params = array_merge($params, $extraparams);

        return $modulelink . '&' . http_build_query($params);
    }
}