<?php
/**
 * The DonDominio Manager Addon for WHMCS.
 *
 * WHMCS version 5.2.x / 5.3.x / 6.x / 7.x
 * @link https://github.com/dondominio/whmcsaddon
 * @package DonDominioWHMCSAddon
 * @license CC BY-ND 3.0 <http://creativecommons.org/licenses/by-nd/3.0/>
 */

use WHMCS\Module\Addon\Dondominio\Controllers\Admin\Dashboard_Controller;
use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Helpers\Template;
use WHMCS\Module\Addon\Dondominio\Helpers\Request;
use Exception;
use Smarty;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define addon module configuration parameters.
 *
 * Includes a number of required system fields including name, description,
 * author, language and version.
 *
 * Also allows you to define any configuration parameters that should be
 * presented to the user when activating and configuring the module. These
 * values are then made available in all module function calls.
 *
 * Examples of each and their possible configuration parameters are provided in
 * the fields parameter below.
 *
 * @return array
 */
function dondominio_config()
{
    return [
        "name" => "DonDominio Manager",
        "description" => "Advanced features from DonDominio.",
        "version" => App::getInstance()->getVersion(),
        "author" => "DonDominio",
        "language" => "english",
        "fields" => [],
    ];
}

/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 * Use this function to perform any database and schema modifications
 * required by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 * @see https://laravel.com/api/6.x/Illuminate/Database/Schema/Blueprint.html
 *
 * @return array Optional success/failure message
 */
function dondominio_activate()
{
    try {
        App::getInstance()->install();

        return [
            'status' => 'success',
            'description' => 'The DonDominio Manager Addon is now ready. Enjoy!'
        ];
    } catch (Exception $e) {
        return [
            'status' => "error",
            'description' => 'Unable to create mod_dondominio: ' . $e->getMessage(),
        ];
    }
}

/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 * Use this function to undo any database and schema modifications
 * performed by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function dondominio_deactivate()
{
    try {
        App::getInstance()->uninstall();

        return [
            'status' => 'success',
            'description' => 'The DonDominio Manager Addon has been successfully disabled.'
        ];
    } catch (Exception $e) {
        return [
            'status' => "error",
            'description' => 'Unable to create mod_dondominio: ' . $e->getMessage(),
        ];
    }
}

/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 * Use this function to perform any required database and schema modifications.
 *
 * This function is optional.
 *
 * @see https://laravel.com/docs/5.2/migrations
 *
 * @return void
 */
function dondominio_upgrade($vars)
{
    try {
        App::getInstance()->upgrade($vars['version']);
    } catch (Exception $e) {
        if (function_exists('logActivity')) {
            $key = 'cant_upgrade_dondominio';
            logActivity(array_key_exists($key, $vars['_lang']) ? $vars['_lang'][$key] : $key);
        }

        throw $e;
    }
}

/**
 * Admin Area Output.
 * 
 * @param array $vars
 *
 */
function dondominio_output($vars)
{
    // Dispatch and handle request here. What follows is a demonstration of one
    // possible way of handling this using a very basic dispatcher implementation.

    $request = Request::getInstance();

    $controller = $request->getParam('__c__');
    $action = $request->getParam('__a__');

    $app = App::getInstance($vars);
    $template = $app->getDispatcher('admin')->dispatch($controller, $action);

    if ($template instanceof Template) {
        $template->getRender()->display(Template::BASE_TEMPLATE);
    } else if ($template instanceof Smarty) {
        $template->display();
    } else {
        echo $template;
    }
}

/**
 * Admin Area Sidebar Output.
 *
 * Used to render output in the admin area sidebar.
 * This function is optional.
 *
 * @param array $vars
 *
 * @return string
 */
function dondominio_sidebar($vars)
{
    $app = App::getInstance($vars);
    $template = $app->getDispatcher('admin')->dispatch(Dashboard_Controller::CONTROLLER_NAME, Dashboard_Controller::VIEW_SIDEBAR);

    if ($template instanceof Template) {
        return $template->getRender()->fetch(Template::BASE_TEMPLATE);
    } else if ($template instanceof Smarty) {
        return $template->fetch();
    } else {
        return $template;
    }
}