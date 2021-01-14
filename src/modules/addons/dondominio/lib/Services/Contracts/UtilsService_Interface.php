<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface UtilsService_Interface
{
    public function isLatestVersion();
    public function findRegistrarModule();
}