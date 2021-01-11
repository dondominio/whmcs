<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface UtilsService_Interface
{
    public function addonIsOutdated();
    public function pluginIsOutdated();
}