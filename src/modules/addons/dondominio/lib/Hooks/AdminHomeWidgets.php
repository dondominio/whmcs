<?php

namespace WHMCS\Module\Addon\Dondominio\Hooks;

use WHMCS\Module\AbstractWidget;
use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Dashboard_Controller;
use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Home_Controller;
use Smarty;
use Exception;

class AdminHomeWidgets extends AbstractWidget
{
    const TEMPLATE_FOLDER = 'widgets';
    const TEMPLATE_FILE = 'admin_home.tpl';

    protected $title = '<img src="https://www.dondominio.com/images/favicon_appletouch.png" class="absmiddle" width="16" height="16"> DonDominio';
    protected $description = '';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;
    protected $requiredPermission = '';

    public function __invoke()
    {
        return new static();
    }

    public function getData()
    {
        $app = App::getInstance();
        $app->setModuleLink('addonmodules.php?module=' . $app->getName());

        try {
            $isLatestVersion = $app->getService('utils')->isLatestVersion();
        } catch (Exception $e) {
            $isLatestVersion = true;
        }

        return [
            'last_version' => $isLatestVersion,
            'links' => [
                'update_modules' => Dashboard_Controller::makeURL(),
                'admin' => Home_Controller::makeURL(),
            ],
            'LANG' => $app->getLang()
        ];
    }

    public function generateOutput($data)
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir(implode(DIRECTORY_SEPARATOR, [dirname(dirname(__DIR__)), 'templates', static::TEMPLATE_FOLDER]));

        foreach ($data as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch(static::TEMPLATE_FILE);
    }
}
