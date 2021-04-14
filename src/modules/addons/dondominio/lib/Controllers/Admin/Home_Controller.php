<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;
use WHMCS\Module\Addon\Dondominio\App;

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
        $app = App::getInstance();

        $checkAPI = (bool) $this->getRequest()->getParam('check_api');
        $info = $app->getInformation($checkAPI);

        $params = [
            'new_version' => isset($info['version']['success']) ? !$info['version']['success'] : false,
            'conection_erro' => isset($info['api']['success']) ? !$info['api']['success'] : true,
            'title' => 'DonDominio',
            'links' => [
                'admin' => Dashboard_Controller::makeURL(),
                'settings' => Settings_Controller::makeURL(),
            ],
            'nav' => static::getNavArray(),
            'print_nav' => false,
        ];

        return $this->view('index', $params);
    }

    /**
     * Return array to mount the dashboard navbar
     * 
     * @return array
     */
    public static function getNavArray()
    {
        $app = App::getInstance();

        return [
            [
                'title' => $app->getLang('menu_status'),
                'link' => Dashboard_Controller::makeURL(),
            ],
            [
                'title' => $app->getLang('menu_tlds_update'),
                'link' => DomainPricings_Controller::makeURL(),
            ],
            [
                'title' => $app->getLang('menu_domains'),
                'link' => Domains_Controller::makeURL(),
            ],
            [
                'title' => $app->getLang('menu_whois'),
                'link' => Whois_Controller::makeURL(),
            ],
        ];
    }
    
}