<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\App;
use Exception;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Dashboard_Controller extends Controller
{
    const CONTROLLER_NAME = '';
    const DEFAULT_TEMPLATE_FOLDER = 'dashboard';

    const VIEW_INDEX = '';
    const VIEW_SIDEBAR = 'sidebar';
    const PRINT_MOREINFO = 'moreapiinfo';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_SIDEBAR => 'view_Sidebar',
            static::PRINT_MOREINFO => 'print_MoreApiInfo'
        ];
    }

    /**
     * View for dashboard
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $app = App::getInstance();

        $params = [
            'version' => $app->getVersion(),
            'checks' => $app->getMinimumRequirements(),
            'links' => [
                'more_api_info' => static::makeURL(static::PRINT_MOREINFO)
            ]
        ];

        return $this->view('index', $params);
    }

    /**
     * View for sidebar
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Sidebar()
    {
        $links = [
            'dashboard' => static::makeURL(),
            'tlds' => DomainPricings_Controller::makeURL(),
            'tlds_new' => DomainPricings_Controller::makeURL(DomainPricings_Controller::VIEW_AVAILABLE_TLDS),
            'domains' => Domains_Controller::makeURL(),
            'transfer' => Domains_Controller::makeURL(Domains_Controller::VIEW_TRANSFER),
            'import' => Domains_Controller::makeURL(Domains_Controller::VIEW_IMPORT),
            'whois' => Whois_Controller::makeURL(Whois_controller::VIEW_INDEX),
            'settings' => Settings_Controller::makeURL(),
        ];

        return $this->view('sidebar', ['links' => $links]);
    }

    /**
     * Retrieves More API Info
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function print_MoreApiInfo()
    {
        ob_start();
        $this->getApp()->getService('api')->printApiInfo();
        $response = nl2br(ob_get_contents());
        ob_end_clean();
        echo $response;
        exit();
    }
}