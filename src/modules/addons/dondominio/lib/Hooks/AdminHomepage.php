<?php

namespace WHMCS\Module\Addon\Dondominio\Hooks;

use Smarty;
use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Dashboard_Controller;

class AdminHomepage
{
    const TEMPLATE_FOLDER = 'widgets';
    const TEMPLATE_FILE = 'popup.tpl';

    public function __invoke()
    {
        return $this->output();
    }

    public function output()
    {
        $app = App::getInstance();
        $app->setModuleLink('addonmodules.php?module=' . $app->getName());
        $settingsService = $app->getService('settings');
        $utilsService = $app->getService('utils');

        if ($utilsService->isLatestVersion()){
            return '';
        }

        $smarty = new Smarty();
        $smarty->setTemplateDir(implode(DIRECTORY_SEPARATOR, [dirname(dirname(__DIR__)), 'templates', static::TEMPLATE_FOLDER]));
        
        $lastVersion = $settingsService->getSetting('last_version');

        $params = [
            'css_path' => sprintf('/modules/addons/%s/css/', $app->getName()),
            'new_version' =>  $lastVersion,
            'version' =>  $app->getVersion(),
            'links' => [ 
                'admin' => Dashboard_Controller::makeURL(),
            ],
            'LANG' => $app->getLang()
        ];

        foreach ($params as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch(static::TEMPLATE_FILE);
    }
}
