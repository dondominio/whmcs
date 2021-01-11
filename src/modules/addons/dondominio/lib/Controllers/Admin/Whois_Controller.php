<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\Helpers\Response;
use Exception;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Whois_Controller extends Controller
{
    const CONTROLLER_NAME = 'whois';
    const DEFAULT_TEMPLATE_FOLDER = 'whois';

    const VIEW_INDEX = '';
    const VIEW_IMPORT = 'viewimport';
    const ACTION_SWITCH = 'switch';
    const ACTION_IMPORT = 'import';
    const ACTION_EXPORT = 'export';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_IMPORT => 'view_Import',
            static::ACTION_SWITCH => 'action_Switch',
            static::ACTION_IMPORT => 'action_Import',
            static::ACTION_EXPORT => 'action_Export'
        ];
    }

    /**
     * Retrieves template for index view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $whoisDomain = $this->getApp()->getService('settings')->getSetting('whois_domain');

        if (strlen($whoisDomain) == 0) {
            return $this->view_Empty();
        }

        $whoisService = $this->getApp()->getService('whois');

        $whoisItems = $whoisService->getWhoisItems();
        $whoisServerFilePath = $whoisService->getWhoisServerFilePath();
        $whoisServerFileIsWritable = is_writable($whoisServerFilePath);

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'whois_server_file_path' => $whoisServerFilePath,
            'whois_server_file_is_writable' => $whoisServerFileIsWritable,
            'whois_items' => $whoisItems,
            'actions' => [
                'switch' => static::ACTION_SWITCH,
            ],
            'links' => [
                'switch' => static::makeURL(static::ACTION_SWITCH, ['tld' => '']),
                'import' => static::makeURL(static::VIEW_IMPORT),
                'export' => static::makeURL(static::ACTION_EXPORT)
            ]
        ];

        return $this->view('index', $params);
    }

    /**
     * Retrieves template for import view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Import()
    {
        $params = [
            'links' => [
                'index' => static::makeURL(),
                'import' => static::makeURL(static::ACTION_IMPORT)
            ]
        ];

        return $this->view('import', $params);
    }

    /**
     * Retrieves template for empty view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Empty()
    {
        $params = [
            'links' => [
                'settings_index' => Settings_Controller::makeURL(Settings_Controller::VIEW_INDEX)
            ]
        ];

        return $this->view('empty', $params);
    }

    /**
     * Action for change whois to Dondominio whois
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Switch()
    {
        $tld = $this->getRequest()->getParam('tld');

        try {
            if (empty($tld)) {
                throw new Exception('new-tld-error');
            }

            $this->getApp()->getService('whois')->setup($tld);

            $this->getResponse()->addSuccess($this->getApp()->getLang('new-tld-ok'));
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Action for import new whois server file
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Import()
    {
        $file = $this->getRequest()->getArrayParam('whoisservers', [], null, $_FILES);

        try {
            if (!array_key_exists('tmp_name', $file) || empty($file['tmp_name'])) {
                throw new Exception('file_not_uploaded');
            }

            $this->getApp()->getService('whois')->importWhois($file);

            $this->getResponse()->addSuccess($this->getApp()->getLang('import-ok'));
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
            return $this->view_import();
        }

        return $this->view_Index();
    }

    /**
     * Action for whois server file export
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Export()
    {
        $whoisService = $this->getApp()->getService('whois');
        $response = $this->getResponse();

        $response->addHeader('Content-Description: File Transfer');
        $response->addHeader('Content-Disposition: attachment; filename="'.basename($whoisService->getWhoisServerFilePath()).'"');
        $response->addHeader('Expires: 0');
        $response->addHeader('Cache-Control: must-revalidate');
        $response->addHeader('Pragma: public');
        $response->addHeader('Content-Length: ' . filesize($whoisService->getWhoisServerFilePath()));

        $response->setContentType(Response::CONTENT_BINARY)->send(readfile($whoisService->getWhoisServerFilePath()), true);
    }
}