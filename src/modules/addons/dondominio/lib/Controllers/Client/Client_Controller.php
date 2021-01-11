<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Client;

class Client_Controller extends Controller
{
    const CONTROLLER_NAME = 'client';
    const DEFAULT_TEMPLATE_FOLDER = 'suggests';

    const VIEW_SUGGESTS = 'viewsuggests';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_SUGGESTS => 'view_Suggests'
        ];
    }

    /**
     * Retrieves template for suggests view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Suggests()
    {
        $currency = $this->getRequest()->getParam('currency', '1');
        $suggestsEnabled = $this->getApp()->getService('settings')->getSetting('suggests_enabled');

        if (!$suggestsEnabled) {
            return '';
        }

        $params = [
            'currency' => $currency,
            'suggests_template' => static::DEFAULT_TEMPLATE_FOLDER . '/suggests_template.tpl'
        ];

        return $this->view('suggests', $params);
    }
}