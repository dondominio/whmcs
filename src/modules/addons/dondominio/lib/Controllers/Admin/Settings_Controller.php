<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\App;
use Exception;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Settings_Controller extends Controller
{
    const CONTROLLER_NAME = 'settings';
    const DEFAULT_TEMPLATE_FOLDER = 'settings';

    const VIEW_INDEX = '';
    const ACTION_SAVE_CREDENTIALS = 'savecredentials';
    const ACTION_SAVE_PRICE_ADJUSTMENT = 'savepriceadjustment';
    const ACTION_SAVE_AUTOMATIC_NOTIFICATIONS = 'saveautomaticnotifications';
    const ACTION_SYNC_TLDS = 'synctlds';
    const ACTION_SAVE_WHOIS_PROXY = 'savewhoisproxy';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::ACTION_SAVE_CREDENTIALS => 'action_SaveCredentials',
            static::ACTION_SAVE_PRICE_ADJUSTMENT => 'action_SavePriceAdjustment',
            static::ACTION_SAVE_AUTOMATIC_NOTIFICATIONS => 'action_SaveAutomaticNotifications',
            static::ACTION_SYNC_TLDS => 'action_SyncTlds',
            static::ACTION_SAVE_WHOIS_PROXY => 'action_SaveWhoisProxy'
        ];
    }

    /**
     * Retrieves template for index view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $settings = $this->getApp()->getService('settings')->findSettingsAsKeyValue();
        $tlds = $this->getApp()->getService('pricing')->findPricingsNotInWatchlist();
        $watchlisted_tlds = $this->getApp()->getService('watchlist')->findWatchlistsOrderedByTld();
        $cacheStatus = $this->getApp()->getService('pricing')->getCacheStatus();
        
         // Actions
        $actions = [];
        foreach (static::getActions() as $key => $action) {
            $actions[$key] = $key;
        }

        // API Username/Password

        $apiUsername = $settings->get('api_username');
        $apiPassword = $settings->get('api_password');

        if (!empty($apiPassword)) {
            $apiPassword = base64_decode($apiPassword);
        }

        if (empty($apiUsername) || empty($apiUsername)) {
            $this->getResponse()->addInfo($this->getApp()->getLang('settings_api_required'));

            // Try to find in Registrar module if credentials not saved yet

            $registrar = new \WHMCS\Module\Registrar();
            $registrar->load('dondominio');
            $registrarConfigArray = $registrar->getSettings();

            if (empty($apiUsername) && array_key_exists('apiuser', $registrarConfigArray)) {
                $apiUsername = $registrarConfigArray['apiuser'];
            }

            if (empty($apiPassword) && array_key_exists('apipasswd', $registrarConfigArray)) {
                $apiPassword = $registrarConfigArray['apipasswd'];
            }
        }

        // WHOIS Placeholders

        $protocol = array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : $_SERVER['REQUEST_SCHEME'];
        $domain = array_key_exists('HTTP_X_FORWARDED_HOST', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['SERVER_NAME'];
        $whoisDomainPlaceholder = (!empty($protocol) ? $protocol . '://' : '') . $domain;

        $whoisIpPlaceholder = array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['SERVER_ADDR'];

        // PARAMS TO TEMPLATE

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'tlds' => $tlds,
            'watchlisted_tlds' => $watchlisted_tlds,
            'last_update' => $cacheStatus->last_update,
            'total_tlds' => $cacheStatus->count,
            'api_username' => $apiUsername,
            'api_password' => $apiPassword,
            'prices_update_cron' => $settings->get('prices_autoupdate') == '1' ? "checked='checked'" : "",
            'register_increase' => $settings->get('register_increase'),
            'transfer_increase' => $settings->get('transfer_increase'),
            'renew_increase' => $settings->get('renew_increase'),
            'notifications_enabled_checkbox' => $settings->get('notifications_enabled') == '1' ? "checked='checked'" : "",
            'notifications_email' => $settings->get('notifications_email'),
            'notifications_new_tlds_checkbox' => $settings->get('notifications_new_tlds') == '1' ? "checked='checked'" : "",
            'notifications_prices_checkbox' => $settings->get('notifications_prices') == '1' ? "checked='checked'" : "",
            'whois_domain' => $settings->get('whois_domain'),
            'whois_ip' => $settings->get('whois_ip'),
            'whois_domain_placeholder' => $whoisDomainPlaceholder,
            'whois_ip_placeholder' => $whoisIpPlaceholder,
            'actions' => $actions,
            'breadcrumbs' => [
                [
                    'title' => $this->getApp()->getLang('menu_status'),
                    'link' => Dashboard_Controller::makeURL()
                ],
                [
                    'title' => $this->getApp()->getLang('settings_title'),
                    'link' => static::makeURL()
                ]
            ]
        ];

        if ($settings->get('register_increase_type') == 'fixed') {
            $params['register_increase_type_fixed'] =  "checked='checked'";
        } else {
            $params['register_increase_type_percent'] = "checked='checked'";
        }

        if ($settings->get('transfer_increase_type') == 'fixed') {
            $params['transfer_increase_type_fixed'] =  "checked='checked'";
        } else {
            $params['transfer_increase_type_percent'] = "checked='checked'";
        }

        if ($settings->get('renew_increase_type') == 'fixed') {
            $params['renew_increase_type_fixed'] =  "checked='checked'";
        } else {
            $params['renew_increase_type_percent'] = "checked='checked'";
        }

        $params['watchlist_is_' . $settings->get('watchlist_mode')] = 'checked="checked"';

        return $this->view('index', $params);
    }

    /**
     * Action for save credentials
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SaveCredentials()
    {
        $username = $this->getRequest()->getParam('api_username');
        $password = $this->getRequest()->getParam('api_password');

        try {
            $this->getApp()->getService('settings')->saveCredentials($username, $password);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
           $this->getResponse()->addError($e->getMessage());
        }

        return $this->view_Index();
    }

    /**
     * Action for save price adjustment
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_savePriceAdjustment()
    {
        $pricesAutoupdate = $this->getRequest()->getParam('prices_update_cron');
        $registerIncrease = $this->getRequest()->getParam('prices_register_add');
        $transferIncrease = $this->getRequest()->getParam('prices_transfer_add');
        $renewIncrease = $this->getRequest()->getParam('prices_renew_add');
        $registerIncreaseType = $this->getRequest()->getParam('prices_register_type');
        $transferIncreaseType = $this->getRequest()->getParam('prices_transfer_type');
        $renewIncreaseType = $this->getRequest()->getParam('prices_renew_type');

        try {
            $settingsService = $this->getApp()->getService('settings');

            $settingsService->setSetting('prices_autoupdate', $pricesAutoupdate == 'on' ? 1 : 0);
            $settingsService->setSetting('register_increase', floatval($registerIncrease));
            $settingsService->setSetting('transfer_increase', floatval($transferIncrease));
            $settingsService->setSetting('renew_increase', floatval($renewIncrease));
            $settingsService->setSetting('register_increase_type', $registerIncreaseType);
            $settingsService->setSetting('transfer_increase_type', $transferIncreaseType);
            $settingsService->setSetting('renew_increase_type', $renewIncreaseType);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
           $this->getResponse()->addError($e->getMessage());
        }

        return $this->view_Index();
    }

    /**
     * Action for save automatic notifications
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SaveAutomaticNotifications()
    {
        $notificationsEnabled = $this->getRequest()->getParam('notifications_enabled');
        $notificationsEmail = $this->getRequest()->getParam('notifications_email');
        $notificationsNewTlds = $this->getRequest()->getParam('notifications_new_tld');
        $notificationPrices = $this->getRequest()->getParam('notifications_prices');
        $watchlistMode = $this->getRequest()->getParam('watchlist');
        $selectedTlds = $this->getRequest()->getArrayParam('watchlisted_tlds', []);

        try {
            $settingsService = $this->getApp()->getService('settings');
            $settingsService->setSetting('notifications_enabled', $notificationsEnabled == 'on' ? 1 : 0);
            $settingsService->setSetting('notifications_email', $notificationsEmail);
            $settingsService->setSetting('notifications_new_tlds', $notificationsNewTlds == 'on' ? 1 : 0);
            $settingsService->setSetting('notifications_prices', $notificationPrices == 'on' ? 1 : 0);
            $settingsService->setSetting('watchlist_mode', $watchlistMode);

            $watchlistService = $this->getApp()->getService('watchlist');
            $watchlistService->updateWatchlist($selectedTlds);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
            $this->getResponse()->addError($e->getMessage());
        }

        return $this->view_Index();
    }

    /**
     * Action for sync TLDs (rebuild cache)
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SyncTlds()
    {
        $cacheRebuild = $this->getRequest()->getParam('cache_rebuild');

        try {
            if ($cacheRebuild != 'on') {
                throw new Exception('Checkbox must be checked for rebuild cache.');
            }

            $this->getApp()->getService('pricing')->apiSync(true);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
            $this->getResponse()->addError($e->getMessage());
        }

        return $this->view_Index();
    }

    /**
     * Action for save whois proxy
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SaveWhoisProxy()
    {
        $whoisDomain = $this->getRequest()->getParam('domain');
        $whoisIp = $this->getRequest()->getParam('ip');
        $redirect = $this->getRequest()->getParam('redirect');

        try {
            $settingsService = $this->getApp()->getService('settings');
            $settingsService->setSetting('whois_domain', $whoisDomain);
            $settingsService->setSetting('whois_ip', $whoisIp);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
            $this->getResponse()->addError($e->getMessage());
        }

        if ($redirect === 'whois'){
            return (new Whois_Controller())->view_Index();
        }

        return $this->view_Index();
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
        $params['nav']= Dashboard_Controller::getNavArray();          
        return parent::view($view, $params);
    }
}