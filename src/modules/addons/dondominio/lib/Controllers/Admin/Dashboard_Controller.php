<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\App;
use Exception;
use WHMCS\Module\Addon\Dondominio\Helpers\Request;
use WHMCS\Module\Addon\Dondominio\Helpers\Response;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Dashboard_Controller extends Controller
{
    const CONTROLLER_NAME = '';
    const DEFAULT_TEMPLATE_FOLDER = 'dashboard';

    const VIEW_INDEX = '';
    const VIEW_BALANCE = 'balance';
    const VIEW_BALANCEUPDATE = 'balanceupdate';
    const VIEW_SIDEBAR = 'sidebar';
    const PRINT_MOREINFO = 'moreapiinfo';
    const UPDATE_MODULES = 'updatemodules';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_BALANCE => 'view_Balance',
            static::VIEW_BALANCEUPDATE => 'view_BalanceUpdate',
            static::VIEW_SIDEBAR => 'view_Sidebar',
            static::PRINT_MOREINFO => 'print_MoreApiInfo',
            static::UPDATE_MODULES => 'update_Modules'
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
        $whmcs = $app->getService('whmcs');

        $checkAPI = (bool) $this->getRequest()->getParam('check_api');

        $params = [
            'whmcs_version' => $app->getService('utils')->getWHMCSVersion(),
            'version' => $app->getVersion(),
            'checks' => $app->getInformation($checkAPI),
            'premium_domains' => (int) $whmcs->isPremiumDomainEnable(),
            'do_check' => $checkAPI,
            'links' => [
                'more_api_info' => static::makeURL(static::PRINT_MOREINFO),
                'update_modules' => static::makeURL(static::UPDATE_MODULES),
                'check_api_status_link' => static::makeURL(static::VIEW_INDEX, ['check_api' => 1]),
                'settings' => Settings_Controller::makeURL(),
            ],
            'breadcrumbs' => [
                [
                    'title' => $app->getLang('menu_status'),
                    'link' => static::makeURL()
                ]
            ]
        ];

        return $this->view('index', $params);
    }

    /**
     * View for API User balance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Balance()
    {
        $app = App::getInstance();
        $api = $app->getService('api');
        $info = $api->getAccountInfo();

        $params = [
            'info' => $info->getResponseData(),
            'links' => [
                'update' => static::makeURL(static::VIEW_BALANCEUPDATE),
            ],
            'breadcrumbs' => [
                [
                    'title' => $app->getLang('menu_status'),
                    'link' => static::makeURL()
                ],
                [
                    'title' =>  $app->getLang('balance_title'),
                    'link' => static::makeURL()
                ]
            ]
        ];

        return $this->view('balance', $params);
    }

    /**
     * JSON with API User balance response
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_BalanceUpdate()
    {
        $response = $this->getResponse();
        $app = App::getInstance();
        $api = $app->getService('api');
        $info = $api->getAccountInfo();

        $response->setContentType(Response::CONTENT_JSON);
        $response->send(json_encode($info->getResponseData()), true);
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
            'deleted' => Domains_Controller::makeURL(Domains_Controller::VIEW_DELETED),
            'whois' => Whois_Controller::makeURL(Whois_controller::VIEW_INDEX),
            'settings' => Settings_Controller::makeURL(),
        ];

        return $this->view('sidebar', [
            'links' => $links,
            'print_nav' => false
        ]);
    }

    /**
     * Retrieves More API Info
     *
     * @return void
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

    /**
     * Downloads and install latest version of Dondominio Modules
     *
     * @return void
     */
    public function update_Modules()
    {
        try {
            $app = App::getInstance();
            $app->getService('utils')->updateModules();

            $this->getResponse()->addSuccess(
                sprintf($this->getApp()->getLang('modules_updated_successfully'))
            );

            $success = true;
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));

            $success = false;
        }

        $params = [
            'success' => $success,
            'print_nav' => false
        ];

        return $this->view('update', $params);
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
        $app = APP::getInstance();
        $params['title'] = $app->getLang('content_title_admin');
        $params['nav']= static::getNavArray();          
        return parent::view($view, $params);
    }

    /**
     * Return array to mount the dashboard navbar
     * 
     * @return array
     */
    public static function getNavArray()
    {
        $app = $app = App::getInstance();
        $controller = Request::getInstance()->getParam('__c__', '');
        $action = Request::getInstance()->getParam('__a__', '');

        return [
            [
                'title' => $app->getLang('status_title'),
                'link' => static::makeURL(static::VIEW_INDEX),
                'selected' => $controller === static::CONTROLLER_NAME && $action === static::VIEW_INDEX
            ],
            [
                'title' => $app->getLang('settings_title'),
                'link' => Settings_Controller::makeURL(),
                'selected' => $controller === Settings_Controller::CONTROLLER_NAME
            ],
            [
                'title' => $app->getLang('balance_title'),
                'link' => static::makeURL(static::VIEW_BALANCE),
                'selected' => $action === static::VIEW_BALANCE
            ],
        ];   
    }

}