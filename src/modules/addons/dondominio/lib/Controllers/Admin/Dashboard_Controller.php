<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Dashboard_Controller extends Controller
{
    const CONTROLLER_NAME = '';
    const DEFAULT_TEMPLATE_FOLDER = 'dashboard';

    const VIEW_INDEX = '';
    const VIEW_SIDEBAR = 'sidebar';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_SIDEBAR => 'view_Sidebar'
        ];
    }

    /**
     * View for dashboard
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $params = [];

        $appVersion = $this->getApp()->getVersion();
        $addonOutdated = $this->getApp()->getService('utils')->addonIsOutdated();
        $pluginOutdated = $this->getApp()->getService('utils')->pluginIsOutdated();

        $params = [
            'version' => $appVersion,
            'addon_outdated' => $addonOutdated,
            'plugin_outdated' => $pluginOutdated,
            'api_info' => ''
        ];

        try {
            ob_start();
            $this->getApp()->getService('api')->printApiInfo();
            $params['api_info'] = nl2br(ob_get_contents());
            ob_end_clean();
        } catch (Exception $e) {
            $params['api_info'] = $this->getApp()->getLang($e->getMessage());
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

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
            'tlds' => DomainPricings_Controller::makeURL(),
            'tlds_new' => DomainPricings_Controller::makeURL(DomainPricings_Controller::VIEW_AVAILABLE_TLDS),
            'domains' => Domains_Controller::makeURL(),
            'transfer' => Domains_Controller::makeURL(Domains_Controller::VIEW_TRANSFER),
            'import' => Domains_Controller::makeURL(Domains_Controller::VIEW_IMPORT),
            'suggests' => Settings_Controller::makeURL(Settings_Controller::VIEW_DOMAIN_SUGGESTIONS),
            'whois' => Whois_Controller::makeURL(Whois_controller::VIEW_INDEX),
            'settings' => Settings_Controller::makeURL(),
        ];

        return $this->view('sidebar', ['links' => $links]);
    }
}