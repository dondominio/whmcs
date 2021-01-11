<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers;

use WHMCS\Module\Addon\Dondominio\Controllers\Contracts\Dispatcher_Interface;
use WHMCS\Module\Addon\Dondominio\Controllers\Client\Client_Controller;
use WHMCS\Module\Addon\Dondominio\Helpers\Template;

class ClientDispatcher implements Dispatcher_Interface
{
    public function getRegisteredControllers()
    {
        return [
            Client_Controller::CONTROLLER_NAME => Client_Controller::class,
        ];
    }

    /**
    * Dispatch request.
    *
    * @param string $requestController Controller
    * @param string $requestAction Action
    *
    * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
    */
    public function dispatch($requestController, $requestAction)
    {
        $controllers = $this->getRegisteredControllers();
        $controllerClass = array_key_exists($requestController, $controllers) ? $controllers[$requestController] : null;

        if (is_null($controllerClass)) {
            return new Template("error.tpl", [
                'error' => "Controller not found: " . $requestController
            ]);
        }

        $controller = new $controllerClass();

        $actions = $controller::getActions();
        $action = array_key_exists($requestAction, $actions) ? $actions[$requestAction] : null;

        if (is_null($action) || !is_callable([$controller, $action])) {
            return new Template("error.tpl", [
                'error' => "Action not found: " . $requestAction
            ]);
        }

        return $controller->$action();
    }
}