<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\App;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Home_Controller extends Controller
{
    const CONTROLLER_NAME = '';
    const DEFAULT_TEMPLATE_FOLDER = 'home';

    const VIEW_INDEX = '';

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
        ];
    }

    /**
     * View for home
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $app = App::getInstance();
        $whmcs = $app->getService('whmcs');
        $api = $app->getService('api');
        $appName = $app->getName();

        $domainFilters = ['registrar' => $appName];
        $totalDomains = $whmcs->getDomainsCount($domainFilters);
        $totalTLDs = $whmcs->getDomainPricingsCount('', $appName);

        $checkAPI = (bool) $this->getRequest()->getParam('check_api');
        $info = $app->getInformation($checkAPI);

        try {
            $certificateResponse = $api->getSSLCertificates(1, 1);
            $totalCertificates = $certificateResponse->get('queryInfo')['total'];
        } catch (\Exception $e){
            $totalCertificates = 0;
        }

        $params = [
            'new_version' => isset($info['version']['success']) ? !$info['version']['success'] : false,
            'conection_erro' => isset($info['api']['success']) ? !$info['api']['success'] : true,
            'total_domains' => $totalDomains,
            'total_tlds' => $totalTLDs,
            'total_ssl' => $totalCertificates,
            'links' => [
                'admin' => Dashboard_Controller::makeURL(),
                'settings' => Settings_Controller::makeURL(),
                'domains' => Domains_Controller::makeURL(Domains_Controller::VIEW_INDEX, $domainFilters),
                'tlds' => DomainPricings_Controller::makeURL(DomainPricings_Controller::VIEW_INDEX, ['autoreg' => $appName]),
                'ssl' => SSL_Controller::makeURL(SSL_Controller::VIEW_INDEX),
            ],
            'print_nav' => false,
        ];

        return $this->view('index', $params);
    }
    
}