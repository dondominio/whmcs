<?php

namespace WHMCS\Module\Addon\Dondominio\Hooks;

use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Controllers\Client\Client_Controller;
use WHMCS\Module\Addon\Dondominio\Helpers\Template;

class ClientAreaFooterOutput
{
    public function __invoke($params)
    {
        $app = App::getInstance($params);

        $current_language = $params['language'];

        $lang_file = $app->getDir() . '/lang/' . $current_language . '.php';

        if (file_exists($lang_file)) {
            include($lang_file);
        } else {
            require($app->getDir() . '/lang/english.php');
        }

        $params['_lang'] = $_ADDONLANG;

        $app = App::getInstance($params);

        $template = $app->getInstance()->getDispatcher('client')->dispatch(
            Client_controller::CONTROLLER_NAME,
            Client_Controller::VIEW_SUGGESTS
        );

        if ($template instanceof Template) {
            return $template->getRender()->fetch(Template::BASE_TEMPLATE);
        } else if ($template instanceof \Smarty) {
            return $template->fetch();
        } else {
            return $template;
        }
    }
}