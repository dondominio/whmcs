<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use Exception;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Settings_Controller extends Controller
{
    const CONTROLLER_NAME = 'settings';
    const DEFAULT_TEMPLATE_FOLDER = 'settings';

    const VIEW_INDEX = '';
    const VIEW_DOMAIN_SUGGESTIONS = 'viewdomainsuggestions';
    const ACTION_SAVE_CREDENTIALS = 'savecredentials';
    const ACTION_SAVE_PRICE_ADJUSTMENT = 'savepriceadjustment';
    const ACTION_SAVE_AUTOMATIC_NOTIFICATIONS = 'saveautomaticnotifications';
    const ACTION_SYNC_TLDS = 'synctlds';
    const ACTION_SAVE_DOMAIN_SUGGESTIONS = 'savedomainsuggestions';
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
            static::VIEW_DOMAIN_SUGGESTIONS => 'view_DomainSuggestionsEnabled',
            static::ACTION_SAVE_CREDENTIALS => 'action_SaveCredentials',
            static::ACTION_SAVE_PRICE_ADJUSTMENT => 'action_SavePriceAdjustment',
            static::ACTION_SAVE_AUTOMATIC_NOTIFICATIONS => 'action_SaveAutomaticNotifications',
            static::ACTION_SYNC_TLDS => 'action_SyncTlds',
            static::ACTION_SAVE_DOMAIN_SUGGESTIONS => 'action_SaveDomainSuggestions',
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

        $suggests_tlds = explode( ',', $settings->get('suggests_tlds'));
		
		foreach ($suggests_tlds as $selected_tld) {
			$tlds_selected[$selected_tld] = "selected=\"selected\"";
        }
        
         // Actions
        $actions = [];
        foreach (static::getActions() as $key => $action) {
            $actions[$key] = $key;
        }

        // PARAMS TO TEMPLATE

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'tlds' => $tlds,
            'watchlisted_tlds' => $watchlisted_tlds,
            'last_update' => $cacheStatus->last_update,
            'total_tlds' => $cacheStatus->count,
            'api_username' => $settings->get('api_username'),
            'api_password' => $settings->get('api_password'),
            'prices_update_cron' => $settings->get('prices_autoupdate') == '1' ? "checked='checked'" : "",
            'register_increase' => $settings->get('register_increase'),
            'transfer_increase' => $settings->get('transfer_increase'),
            'renew_increase' => $settings->get('renew_increase'),
            'notifications_enabled_checkbox' => $settings->get('notifications_enabled') == '1' ? "checked='checked'" : "",
            'notifications_email' => $settings->get('notifications_email'),
            'notifications_new_tlds_checkbox' => $settings->get('notifications_new_tlds') == '1' ? "checked='checked'" : "",
            'notifications_prices_checkbox' => $settings->get('notifications_prices') == '1' ? "checked='checked'" : "",
            'suggests_enabled' => $settings->get('suggests_enabled') ? "checked='checked'" : '',
            'lang_selected' => [$settings->get('suggests_language') => 'selected=\"selected\"'],
            'tlds_selected' => $tlds_selected,
            'whois_domain' => $settings->get('whois_domain'),
            'whois_ip' => $settings->get('whois_ip'),
            'actions' => $actions
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
     * Retrieves template for domain suggestions settings
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_DomainSuggestionsEnabled()
    {
        $enabled = $this->getApp()->getService('settings')->getSetting('suggests_enabled');

        $params = [
            'enabled_status' => $enabled == 1 ? $this->getApp()->getLang('suggests_enabled') : $this->getApp()->getLang('suggests_disabled'),
            'settings_link' => static::makeURL()
        ];

        return $this->view('domainsuggestions', $params);
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
     * Action for save domain suggestions settings
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SaveDomainSuggestions()
    {
        $suggestsEnabled = $this->getRequest()->getParam('suggests_enabled');
        $suggestsLanguage = $this->getRequest()->getParam('language');
        $suggestsTlds = $this->getRequest()->getArrayParam('tlds');

        try {
            $settingsService = $this->getApp()->getService('settings');
            $settingsService->setSetting('suggests_enabled', $suggestsEnabled == 'on' ? 1 : 0);
            $settingsService->setSetting('suggests_language', $suggestsLanguage);
            $settingsService->setSetting('suggests_tlds', implode(',', $suggestsTlds));

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

        try {
            $settingsService = $this->getApp()->getService('settings');
            $settingsService->setSetting('whois_domain', $whoisDomain);
            $settingsService->setSetting('whois_ip', $whoisIp);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
            $this->getResponse()->addError($e->getMessage());
        }

        return $this->view_Index();
    }
}