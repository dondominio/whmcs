<?php

namespace WHMCS\Module\Server\Dondominiossl\Controllers;


class ClientController extends \WHMCS\Module\Server\Dondominiossl\Controllers\Base
{
    const VIEW_INDEX = 'index'; 
    const VIEW_REISSUE = 'reissue'; 
    const ACTION_REISSUE = 'actionreissue'; 
    const VIEW_CHANGEMETHOD = 'changemethod'; 
    const ACTION_CHANGEMETHOD = 'actionchangemethod'; 

    protected function getViews(): array
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_REISSUE => 'view_Reissue',
            static::ACTION_REISSUE => 'action_Reissue',
            static::VIEW_CHANGEMETHOD => 'view_ChangeMethod',
            static::ACTION_CHANGEMETHOD => 'action_ChangeMethod',
        ];
    }

    public function process(): array
    {
        $response = parent::process();

        if (empty($response)){
            return $this->view_Index();
        }

        return $response;
    }

    protected function view_Index(): array
    {
        $api = $this->app->getApiService();
        $certificateID = $this->app->getParams()['customfields'][\WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID];
        $getInfoResponse = $api->getCertificateInfo($certificateID);
        $ddProduct = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::where(['dd_product_id' => $getInfoResponse->get('productID')])->first();

        return $this->send('templates/overview.tpl', [
            'api_response' => $getInfoResponse->getResponseData(),
            'dd_product_name' => $ddProduct->product_name
        ]);
    }

    protected function view_Reissue(): array
    {
        $user = $this->app->getParams()['clientsdetails'];
        $domain = $this->app->getParams()['domain'];

        return $this->send('templates/reissue.tpl', [
            'user' => $user,
            'domain' => $domain,
            'links' => [
                'action_reissue' => $this->createUrl(static::ACTION_REISSUE),
            ],
        ]);
    }

    protected function action_Reissue(): array
    {
        $CSRArgs = [
            'commonName' => $this->getRequest()->getParam('common_name'),
            'organizationName' => $this->getRequest()->getParam('organization_name'),
            'organizationalUnitName' => $this->getRequest()->getParam('organization_unit_name'),
            'countryName' => $this->getRequest()->getParam('country_name'),
            'stateOrProvinceName' => $this->getRequest()->getParam('state_or_province_name'),
            'localityName' => $this->getRequest()->getParam('location_name'),
            'emailAddress' => $this->getRequest()->getParam('email_address'),
        ];

        $reissueResponse = $this->app->reissue($CSRArgs);

        $response = [
            'success' => $reissueResponse === 'success',
            'msg' => $reissueResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response),true);

        return $response;
    }

    protected function view_ChangeMethod(): array
    {
        $certificateID = $this->app->getParams()['customfields'][\WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID];
        $domains = [];
        $validationMethods = [
            'mail' => 'Validación por correo electrónico',
            'dns' => 'Validación mediante registro dns en la zona dns del dominio',
            'http' => 'Validación mediante protocolo http',
            'https' => 'Validación mediante protocolo https',
        ];

        $certificates = $this->app->getApiService()->getCertificateInfo($certificateID, 'validationStatus');
        $certificatesData = $certificates->getResponseData();
        $dcv = $certificatesData['validationData']['dcv'];

        foreach ($dcv as $val) {
            $domains[$val['domainName']] = $val['domainName'];
        }

        return $this->send('templates/changevalidatonmethod.tpl', [
            'validation_methods' => $validationMethods,
            'domains' => $domains,
            'links' => [
                'changemethod' => $this->createUrl(static::ACTION_CHANGEMETHOD),
            ]
        ]);
    }

    protected function action_ChangeMethod(): array
    {
        $domain = $this->getRequest()->getParam('common_name', '');
        $method = $this->getRequest()->getParam('validation_method', '');

        $changeMethodResponse = $this->app->changeValidationMethod($domain, $method);

        $response = [
            'success' => $changeMethodResponse === 'success',
            'msg' => $changeMethodResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response),true);

        return $response;
    }
}

