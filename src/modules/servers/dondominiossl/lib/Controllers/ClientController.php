<?php

namespace WHMCS\Module\Server\Dondominiossl\Controllers;


class ClientController extends \WHMCS\Module\Server\Dondominiossl\Controllers\Base
{
    const VIEW_INDEX = 'index';
    const VIEW_VALIDATION = 'validation';
    const VIEW_REISSUE = 'reissue';
    const ACTION_REISSUE = 'actionreissue';
    const ACTION_CHANGEMETHOD = 'actionchangemethod';
    const ACTION_RESEND_MAIL = 'actionresendmail';
    const ACTION_DOWNLOAD_CRT = 'downloadcrt';

    protected function getViews(): array
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_VALIDATION => 'view_Validation',
            static::VIEW_REISSUE => 'view_Reissue',
            static::ACTION_REISSUE => 'action_Reissue',
            static::ACTION_CHANGEMETHOD => 'action_ChangeMethod',
            static::ACTION_RESEND_MAIL => 'action_ResendValidationMail',
            static::ACTION_DOWNLOAD_CRT => 'action_DownloadCRT',
        ];
    }

    public function process(): array
    {
        $response = parent::process();

        if (empty($response)) {
            return $this->view_Index();
        }

        return $response;
    }

    protected function getValidationMethods(): array
    {
        return [
            'mail' => 'Validación por correo electrónico',
            'dns' => 'Validación mediante dns',
            'http' => 'Validación mediante protocolo http',
            'https' => 'Validación mediante protocolo https',
        ];
    }

    protected function getValidationStatus(): array
    {
        return [
            'process' => 'En proceso',
            'valid' => 'Válido',
            'expired' => 'Expirado',
            'renew' => 'Renovación en proceso',
            'reissue' => 'En proceso de reemisión',
            'cancel' => 'Cancelado',
        ];
    }

    protected function getDownloadInfoTypes(): array
    {
        $downloadTypes = [
            'zip' => [
                'name' => 'ZIP',
                'need_pass' => false
            ],
            'pem' => [
                'name' => 'PEM',
                'need_pass' => false
            ],
            'der' => [
                'name' => 'DER/CER',
                'need_pass' => false
            ],
            'p7b' => [
                'name' => 'P7B/PKCS#7',
                'need_pass' => false
            ],
            'pfx' => [
                'name' => 'PFX/PKCS#12',
                'need_pass' => true
            ],
        ];

        return $downloadTypes;
    }

    protected function view_Index(): array
    {
        $infoResponse = $this->getApp()->getCertificateInfo('ssldata');
        $downloadTypes = $this->getDownloadInfoTypes();
        $certificate = [];
        $crtStatus = '';

        if (is_object($infoResponse)) {
            $ddProduct = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::where(['dd_product_id' => $infoResponse->get('productID')])->first();
            $ddProductName = is_object($ddProduct) ? $ddProduct->product_name : '';
            $certificate = $infoResponse->getResponseData();
            $crtStatus = $infoResponse->get('status');

            if (empty($infoResponse->get('sslKey'))) {
                unset($downloadTypes['pfx']);
            }
        }

        return $this->send('templates/overview.tpl', [
            'certificate' => $certificate,
            'dd_product_name' => $ddProductName,
            'in_process' => $crtStatus === 'process',
            'is_valid' => $crtStatus === 'valid',
            'links' => [
                'download_crt' => $this->buildUrl(static::ACTION_DOWNLOAD_CRT),
                'viewreissue' => $this->buildUrl(static::VIEW_REISSUE),
                'validation' => $this->buildUrl(static::VIEW_VALIDATION),
            ]
        ]);
    }

    protected function view_Validation(): array
    {
        $infoResponse = $this->getApp()->getCertificateInfo('validationStatus');
        $validationMethods = $this->getValidationMethods();
        $crtStatus = '';
        $certificate = [];
        $domains = [];

        if (is_object($infoResponse)) {
            $certificate = $infoResponse->getResponseData();
            $crtStatus = $infoResponse->get('status');

            $status = $this->getValidationStatus();
            $certificate['displayStatus'] = isset($status[$crtStatus]) ? $status[$crtStatus] : $crtStatus;
            $domains = isset($certificate['validationData']['dcv']) ? $certificate['validationData']['dcv'] : [];

            foreach ($domains as $key => $domain) {
                $method = $domain['method'];
                $displayValidationMethod = isset($validationMethods[$method]) ? $validationMethods[$method] : $method;
                $domains[$key]['displayValidationMethod'] = $displayValidationMethod;
                $domains[$key]['validationMails'] = implode(',', $this->getApp()->getCommonNameValidationEmails($key));
            }
        }

        unset($validationMethods['mail']);

        return $this->send('templates/validation.tpl', [
            'certificate' => $certificate,
            'domains' => $domains,
            'validation_methods' => $validationMethods,
            'can_change_validation' => in_array($crtStatus, ['process', 'reissue']),
            'in_process' => $crtStatus === 'process',
            'is_valid' => $crtStatus === 'valid',
            'links' => [
                'changemethod' => $this->buildUrl(static::ACTION_CHANGEMETHOD),
                'resendmail' => $this->buildUrl(static::ACTION_RESEND_MAIL),
            ]
        ]);
    }

    protected function view_Reissue(): array
    {
        $getInfoResponse = $this->getApp()->getCertificateInfo('validationStatus');
        $user = $this->getApp()->getParams()['clientsdetails'];
        $domain = $this->getApp()->getParams()['domain'];

        return $this->send('templates/reissue.tpl', [
            'user' => $user,
            'domain' => $domain,
            'certificate' => is_object($getInfoResponse) ? $getInfoResponse->getResponseData() : [],
            'validation_methods' => $this->getValidationMethods(),
            'links' => [
                'action_reissue' => $this->buildUrl(static::ACTION_REISSUE),
                'index' => $this->buildUrl(static::VIEW_INDEX),
            ],
        ]);
    }

    protected function action_Reissue(): array
    {
        $CSRArgs = [
            'organizationName' => $this->getRequest()->getParam('organization_name'),
            'organizationalUnitName' => $this->getRequest()->getParam('organization_unit_name'),
            'countryName' => $this->getRequest()->getParam('country_name'),
            'stateOrProvinceName' => $this->getRequest()->getParam('state_or_province_name'),
            'localityName' => $this->getRequest()->getParam('location_name'),
            'emailAddress' => $this->getRequest()->getParam('email_address'),
        ];

        $altNames = $this->getRequest()->getArrayParam('alt_name', []);
        $altValidations = $this->getRequest()->getArrayParam('alt_validation', []);

        $validationMethod = $this->getRequest()->getParam('validation_method');

        $reissueResponse = $this->getApp()->reissue($CSRArgs, $validationMethod, $altNames, $altValidations);
        $isSuccess = $reissueResponse === 'success';

        $response = [
            'success' => $isSuccess,
            'msg' => $isSuccess ? 'Certificado remitido correctamente' : $reissueResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);

        return $response;
    }

    protected function action_ChangeMethod(): array
    {
        $commonName = $this->getRequest()->getParam('common_name', '');
        $method = $this->getRequest()->getParam('validation_method', '');

        $changeMethodResponse = $this->getApp()->changeValidationMethod($commonName, $method);
        $isSuccess = $changeMethodResponse === 'success';

        $response = [
            'success' => $isSuccess,
            'msg' => $isSuccess ? 'Metodo cambiado correctamente' : $changeMethodResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);

        return $response;
    }

    protected function action_ResendValidationMail(): array
    {
        $commonName = $this->getRequest()->getParam('common_name', '');
        $resendResponse = $this->getApp()->resendValidationMail($commonName);
        $isSuccess = $resendResponse === 'success';

        $response = [
            'success' => $isSuccess,
            'msg' => $isSuccess ? 'Correo reenviado correctamente' : $resendResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);

        return $response;
    }

    protected function action_DownloadCRT(): array
    {
        $infoType = $this->getRequest()->getParam('type', 'zip');
        $pass = $this->getRequest()->getParam('password', '');

        $response = $this->getApp()->getCertificateInfo($infoType, $pass);

        if (empty($response) || !is_array($response->get('content'))) {
            $this->setErrorMsg('Error al obtener el archivo');
            return $this->view_Index();
        }

        $content = $response->get('content');

        $type = isset($content['type']) ? $content['type'] : null;
        $name = isset($content['name']) ? $content['name'] : null;
        $encoded = isset($content['base64encoded']) ? $content['base64encoded'] : null;
        $data = isset($content['data']) ? $content['data'] : null;

        if ($encoded) {
            $data = base64_decode($data);
        }

        header(sprintf('Content-type: %s', $type));
        header(sprintf('Content-Length: %s', strlen($data)));
        header(sprintf('Content-Disposition: attachment; filename=%s', $name));

        echo $data;

        die();
    }
}
