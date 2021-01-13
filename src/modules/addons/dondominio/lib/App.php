<?php

namespace WHMCS\Module\Addon\Dondominio;

use WHMCS\Module\Addon\Dondominio\Controllers\AdminDispatcher;
use WHMCS\Module\Addon\Dondominio\Controllers\ClientDispatcher;
use WHMCS\Module\Addon\Dondominio\Helpers\Migrations;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\PricingService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\SettingsService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\WatchlistService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\WHMCSService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\WhoisService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\APIService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\TldSettingsService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\EmailService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\UtilsService_Interface;
use WHMCS\Module\Addon\Dondominio\Services\Pricing_Service;
use WHMCS\Module\Addon\Dondominio\Services\Settings_Service;
use WHMCS\Module\Addon\Dondominio\Services\Watchlist_Service;
use WHMCS\Module\Addon\Dondominio\Services\TldSettings_Service;
use WHMCS\Module\Addon\Dondominio\Services\WHMCS_Service;
use WHMCS\Module\Addon\Dondominio\Services\API_Service;
use WHMCS\Module\Addon\Dondominio\Services\Whois_Service;
use WHMCS\Module\Addon\Dondominio\Services\Email_Service;
use WHMCS\Module\Addon\Dondominio\Services\Utils_Service;
use Exception;

class App
{
    const NAME = 'dondominio';
    const UNKNOWN_VERSION = 'unknown';

    protected static $instance;

    protected array $dispatchers = [];
    protected array $services = [];
    protected array $whmcsVars = [];
    protected array $lang = [];
    protected $moduleLink = '';

    /**
     * Get App Instance
     */
    public static function getInstance(array $vars = null)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
            static::$instance->dispatchers = [
                'admin' => new AdminDispatcher(),
                'client' => new ClientDispatcher()
            ];
        }

        if (!is_null($vars)) {
            static::$instance->whmcsVars = $vars;

            if (array_key_exists('_lang', $vars)) {
                static::$instance->setLang($vars['_lang']);
            }
    
            if (array_key_exists('modulelink', $vars)) {
                static::$instance->setModuleLink($vars['modulelink']);
            }
        }

        return static::$instance;
    }

    /**
     * Get App Name
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Get dispatchers
     *
     * @return array
     */
    public function getDispatchers()
    {
        return $this->dispatchers;
    }

    /**
     * Get specific dispatcher
     *
     * @return null|\WHMCS\Module\Addon\Dondominio\Controllers\Dispatchers\Dispatcher_Interface
     */
    public function getDispatcher($key)
    {
        if (!array_key_exists($key, $this->getDispatchers())) {
            return null;
        }

        return $this->dispatchers[$key];
    }

    /**
     * Get Services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Get Specific Service
     *
     * @throws \Exception if no service has found
     *
     * @return \WHMCS\Module\Addon\Dondominio\Services\Service
     */
    public function getService($key)
    {
        if (!array_key_exists($key, $this->getServices())) {
            switch ($key) {
                case 'pricing':
                    $this->setPricingService(new Pricing_Service());
                break;
                case 'settings':
                    $this->setSettingsService(new Settings_Service());
                break;
                case 'watchlist':
                    $this->setWatchlistService(new Watchlist_Service());
                break;
                case 'tld_settings':
                    $this->setTldSettings(new TldSettings_Service());
                break;
                case 'whmcs':
                    $this->setWHMCSService(new WHMCS_Service());
                break;
                case 'whois':
                    $this->setWhoisService(new Whois_Service());
                break;
                case 'email':
                    $this->setEmailService(new Email_Service());
                break;
                case 'api':
                    $apiService = new API_Service([
                        'apiuser' => $this->getService('settings')->getSetting('api_username'),
                        'apipasswd' => base64_decode($this->getService('settings')->getSetting('api_password')),
                        'userAgent' => ['DomainManagementAddonForWHMCS' => static::getVersion()]
                    ]);
            
                    $this->setAPIService($apiService);
                break;
                case 'utils':
                    $this->setUtilsService(new Utils_Service());
                break;
                default:
                    throw new Exception('[Fatal error] Service ' . $key . ' doesnt exists.');
                break;
            }
        }

        return $this->services[$key];
    }

    /**
     * Setter for Pricing Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\PricingService_Interface $service
     *
     * @return void
     */
    public function setPricingService(PricingService_Interface $service)
    {
        $this->services['pricing'] = $service;
    }

    /**
     * Setter for Settings Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\SettingsService_Interface $service
     *
     * @return void
     */
    public function setSettingsService(SettingsService_Interface $service)
    {
        $this->services['settings'] = $service;
    }

    /**
     * Setter for Watchlist Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\WatchlistService_Interface $service
     *
     * @return void
     */
    public function setWatchlistService(WatchlistService_Interface $service)
    {
        $this->services['watchlist'] = $service;
    }

    /**
     * Setter for TLD Settings Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\TldSettingsService_Interface $service
     *
     * @return void
     */
    public function setTldSettings(TldSettingsService_Interface $service)
    {
        $this->services['tld_settings'] = $service;
    }

    /**
     * Setter for WHMCS Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\WHMCSService_Interface $service
     *
     * @return void
     */
    public function setWHMCSService(WHMCSService_Interface $service)
    {
        $this->services['whmcs'] = $service;
    }

    /**
     * Setter for Whois Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\WhoisService_Interface $service
     *
     * @return void
     */
    public function setWhoisService(WhoisService_Interface $service)
    {
        $this->services['whois'] = $service;
    }

    /**
     * Setter for Email Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\WhoisService_Interface $service
     *
     * @return void
     */
    public function setEmailService(EmailService_Interface $service)
    {
        $this->services['email'] = $service;
    }

    /**
     * Setter for API Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\APIService_Interface $service
     *
     * @return void
     */
    public function setAPIService(APIService_Interface $service)
    {
        $this->services['api'] = $service;
    }

    /**
     * Setter for Utils Service
     *
     * @param \WHMCS\Module\Addon\Dondominio\Services\Contracts\UtilsService_Interface $service
     *
     * @return void
     */
    public function setUtilsService(UtilsService_Interface $service)
    {
        $this->services['utils'] = $service;
    }

    /**
     * Gets WHMCS Vars
     *
     * @return array
     */
    public function getWHMCSVars()
    {
        return $this->whmcsVars;
    }

    /**
     * Get specific WHMCS Var or null if not found
     *
     * @param string $key Key
     *
     * @return mixed|null
     */
    public function getWHMCSVar($key)
    {
        return isset($key, $this->whmcsVars) ? $this->whmcsVars[$key] : null;
    }

    /**
     * Set WHMCSVars
     *
     * @param array $whmcsVars
     *
     * @return void
     */
    public function setWHMCSVars(array $whmcsVars)
    {
        $this->whmcsVars = $whmcsVars;
    }

    /**
     * Get entire language array or specific key in language array
     *
     * @param string|null $key Specific key to search inside language array. If null, will return whole language array
     *
     * @return mixed
     */
    public function getLang($key = null)
    {
        if (!is_null($key)) {
            return array_key_exists($key, $this->lang) ? $this->lang[$key] : $key;
        }

        return $this->lang;
    }

    /**
     * Set language
     *
     * @param array $lang
     *
     * @return void
     */
    public function setLang(array $lang)
    {
        $this->lang = $lang;
    }

    /**
     * Getter of module link
     *
     * @see https://developers.whmcs.com/addon-modules/admin-area-output/
     *
     * @return string
     */
    public function getModuleLink()
    {
        return $this->moduleLink;
    }

    /**
     * Setter of module Link
     *
     * @see https://developers.whmcs.com/addon-modules/admin-area-output/
     *
     * @param string $moduleLink
     *
     * @return void
     */
    public function setModuleLink($moduleLink)
    {
        $this->moduleLink = $moduleLink;
    }

    /**
     * Returns App directory
     *
     * @return string
     */
    public static function getDir()
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
    }

    /**
     * Returns version.json file path
     *
     * @return string
     */
    public static function getVersionFilePath()
    {
        return static::getDir() . DIRECTORY_SEPARATOR . 'version.json';
    }

    /**
     * Returns App's version
     *
     * @return string
     */
    public static function getVersion()
    {
        $versionFile = static::getVersionFilePath();

        if (!file_exists($versionFile)) {
            return static::UNKNOWN_VERSION;
        }

        $json = @file_get_contents($versionFile);

        if (empty($json)) {
            return static::UNKNOWN_VERSION;
        }

        $versionInfo = json_decode($json, true);

        if (!is_array($versionInfo) || !array_key_exists('version', $versionInfo)) {
            return static::UNKNOWN_VERSION;
        }

        return $versionInfo['version'];
    }

    /**
     * Installs the application
     *
     * @throws \Exception If currency EUR is not installed
     */
    public static function install()
    {
        $app = new static();

        $currency = $app->getService('whmcs')->getCurrency('EUR');

        if (is_null($currency)) {
            throw new Exception('The DonDominio API works with Euros (EUR). Please, add this currency to your WHMCS configuration before enabling the Addon.');
        }

        Migrations::install();
    }

    /**
     * Uninstalls the application
     *
     * @return void
     */
    public static function uninstall()
    {
        Migrations::uninstall();
    }

    /**
     * Upgrades the application to the latest version
     *
     * @return void
     */
    public static function upgrade($version)
    {
        Migrations::upgrade($version);
    }
}