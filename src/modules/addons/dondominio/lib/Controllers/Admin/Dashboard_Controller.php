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
    const CONTROLLER_NAME = 'dashboard';
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
        $info = $app->getInformation($checkAPI);

        $params = [
            'whmcs_version' => $app->getService('utils')->getWHMCSVersion(),
            'version' => $app->getVersion(),
            'checks' => $info,
            'premium_domains' => (int) $whmcs->isPremiumDomainEnable(),
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

        if ($checkAPI) {
            $this->addAPICHeck($info);
        }

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
        $app = $this->getApp();
        $controller = $this->getRequest()->getParam('__c__', '');

        $sidebar = [
            [
                'link' => Home_Controller::makeURL(),
                'title' => $app->getLang('menu_home'),
                'selected' => Home_Controller::CONTROLLER_NAME === $controller
            ],
            [
                'link' => Dashboard_Controller::makeURL(),
                'title' => $app->getLang('menu_status'),
                'selected' => Dashboard_Controller::CONTROLLER_NAME === $controller || Settings_Controller::CONTROLLER_NAME === $controller
            ],
            [
                'link' => DomainPricings_Controller::makeURL(),
                'title' => $app->getLang('menu_tlds_update'),
                'selected' => DomainPricings_Controller::CONTROLLER_NAME === $controller
            ],
            [
                'link' => Domains_Controller::makeURL(),
                'title' => $app->getLang('menu_domains'),
                'selected' => Domains_Controller::CONTROLLER_NAME === $controller
            ],
            [
                'link' => Whois_Controller::makeURL(),
                'title' => $app->getLang('menu_whois'),
                'selected' => Whois_Controller::CONTROLLER_NAME === $controller
            ],
        ];

        return $this->view('sidebar', [
            'sidebar' => $sidebar,
            'print_nav' => false,
            'print_title' => false,
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
            'print_nav' => false,
            'print_title' => false,
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
        $params['nav'] = static::getNavArray();
        return parent::view($view, $params);
    }

    /**
     * Add error or success of api conexion
     * 
     * @param array $info Result of APP GetInformation
     * 
     * @return void
     */
    protected function addAPICheck($info)
    {
        $app = $this->getApp();
        $success = isset($info['api']['success']) ? $info['api']['success'] : false;
        $error = $app->getLang('error');

        if($success) {
            $this->getResponse()->addSuccess($app->getLang('success_api_conection'));
            return;
        }

        $error = isset($info['api']['message']) ? $info['api']['message'] : $error; 

        $this->getResponse()->addError($error);
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
