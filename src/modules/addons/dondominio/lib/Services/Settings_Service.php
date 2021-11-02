<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\SettingsService_Interface;
use WHMCS\Module\Addon\Dondominio\Models\Settings_Model;
use DateTime;

class Settings_Service extends AbstractService implements SettingsService_Interface
{
    const TS_FORMAT = 'Y-m-d H:i:s';

    /**
     * Retreive a setting by key
     *
     * @return mixed
     */
    public static function getSetting($key)
    {
        return Settings_Model::select('value')->where('key', $key)->value('value');
    }

    /**
     * Retreive a setting in DateTime by key
     *
     * @return DateTime
     */
    public static function getTsSetting($key)
    {
        $ts = Settings_Model::select('value')->where('key', $key)->value('value');

        $dateTime = DateTime::createFromFormat(static::TS_FORMAT, $ts);

        if ($dateTime instanceof DateTime){
            return $dateTime;
        }

        return new DateTime();
    }


    /**
     * Store a setting by key, $value
     *
     * @param string $key Key
     * @param string value Value
     *
     * @return bool
     */
    public static function setSetting($key, $value)
    {
        return Settings_Model::updateOrInsert(['key' => $key], ['value' => $value]);
    }

    /**
     * return table as collection of  ['key' => 'value']
     *
     * @return \Illuminate\Support\Collection
     */
    public function findSettingsAsKeyValue()
    {
        return Settings_Model::get()->pluck('value', 'key');
    }

    /**
     * Save credentials for api connection and performs initial sync if needed
     *
     * @param string $username API username
     * @param string $password API password
     *
     * @return void
     */
    public function saveCredentials($username, $password)
    {
        $preUsername = static::getSetting('api_username');
        $prePassword = static::getSetting('api_password');

        // Save in DB
        static::setSetting('api_username', $username);
        static::setSetting('api_password', base64_encode($password));

        // Save in Registrar Module
        /*
        if ($this->getApp()->getService('utils')->isRegistrarModuleActive()) {
            try {
                $options = [
                    'moduleType' => 'registrar',
                    'moduleName' => 'dondominio',
                    'parameters' => [
                        'apiuser' => $username,
                        'apipasswd' => $password
                    ]
                ];

                $this->getApp()->getService('whmcs')->doLocalAPICall("UpdateModuleConfiguration", $options);
            } catch (Exception $e) {
                logActivityCall($e->getMessage());
            }
        }
        */

        $this->getApp()->getService('api')->reload([
            'apiuser' => $username,
            'apipasswd' => $password,
        ]);

        if (strlen($preUsername) > 0 || strlen($prePassword) > 0) {
            return;
        }

        $this->getApp()->getService('pricing')->apiSync(true);
    }
}