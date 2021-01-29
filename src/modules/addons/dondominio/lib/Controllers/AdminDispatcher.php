<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Controllers\Contracts\Dispatcher_Interface;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Dashboard_Controller;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\DomainPricings_Controller;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Settings_Controller;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Domains_Controller;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Whois_Controller;
use WHMCS\Module\Addon\Dondominio\Helpers\Template;

class AdminDispatcher implements Dispatcher_Interface
{
    /**
     * Gets available controllers
     * 
     * @return array
     */
    public function getRegisteredControllers()
    {
        return [
            Dashboard_Controller::CONTROLLER_NAME => Dashboard_Controller::class,
            DomainPricings_Controller::CONTROLLER_NAME => DomainPricings_Controller::class,
            Domains_controller::CONTROLLER_NAME => Domains_controller::class,
            Settings_Controller::CONTROLLER_NAME => Settings_Controller::class,
            Whois_Controller::CONTROLLER_NAME => Whois_controller::class,
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
        // Check if API Username & Password is set
        // Otherwise, redirect to settings

        $skipActions = ($requestController == Settings_Controller::CONTROLLER_NAME && $requestAction == Settings_Controller::ACTION_SAVE_CREDENTIALS)
        || ($requestController == Dashboard_Controller::CONTROLLER_NAME && $requestAction == Dashboard_Controller::VIEW_SIDEBAR);

        if (!$skipActions) {
            $username = App::getInstance()->getService('settings')->getSetting('api_username');
            $password = App::getInstance()->getService('settings')->getSetting('api_password');

            if (strlen($username) == 0 || strlen($password) == 0) {
                $requestController = Settings_Controller::CONTROLLER_NAME;
                $requestAction = Settings_Controller::VIEW_INDEX;
            }
        }

        if (strlen($requestController) == 0) {
            $requestController = Dashboard_Controller::CONTROLLER_NAME;
        }

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