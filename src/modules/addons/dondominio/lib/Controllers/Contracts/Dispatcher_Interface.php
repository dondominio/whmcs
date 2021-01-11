<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Contracts;

interface Dispatcher_Interface
{
    public function getRegisteredControllers();
    public function dispatch($requestController, $requestAction);
    
}