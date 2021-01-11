<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface SettingsService_Interface
{
    public static function getSetting($key);
    public static function setSetting($key, $value);
    public function findSettingsAsKeyValue();
    public function saveCredentials($username, $password);
}