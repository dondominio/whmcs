<?php

namespace WHMCS\Module\Registrar\Dondominio\Services\Contracts;

interface ImportService_Interface
{
    public function sync();
    public static function displayVersion();
}