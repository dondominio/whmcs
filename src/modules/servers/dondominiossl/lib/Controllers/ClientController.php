<?php

namespace WHMCS\Module\Server\Dondominiossl\Controllers;


class ClientController extends \WHMCS\Module\Server\Dondominiossl\Controllers\Base
{
    const VIEW_VALIDATION = 'validation';
    const VIEW_REISSUE = 'reissue';
    const ACTION_REISSUE = 'actionreissue';
    const ACTION_CHANGEMETHOD = 'actionchangemethod';
    const ACTION_RESEND_MAIL = 'actionresendmail';
    const ACTION_DOWNLOAD_CRT = 'downloadcrt';
    const ACTION_GET_DOMAIN_MAILS = 'getdomainmails';

    /**
     * Return list with the controller views/actions
     * 
     * @return array
     */
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
            static::ACTION_GET_DOMAIN_MAILS => 'action_GetDomainMails',
        ];
    }

    /**
     * Process a request and return a array for dondominiossl_ClientArea
     * 
     * @return array
     */
    public function process(): array
    {
        $response = parent::process();

        if (empty($response)) {
            return $this->view_Index();
        }

        return $response;
    }

    /**
     * Return array with the validation methods
     * 
     * @return array
     */
    protected function getValidationMethods(): array
    {
        return [
            'mail' =>  $this->translate('cert_validation_mail'),
            'dns' => $this->translate('cert_validation_dns'),
            'http' =>  $this->translate('cert_validation_http'),
            'https' =>  $this->translate('cert_validation_https'),
        ];
    }

    /**
     * Return array with the validation status
     * 
     * @return array
     */
    protected function getValidationStatus(): array
    {
        return [
            'process' => $this->translate('cert_status_process'),
            'valid' => $this->translate('cert_status_valid'),
            'expired' => $this->translate('cert_status_expired'),
            'renew' => $this->translate('cert_status_renew'),
            'reissue' => $this->translate('cert_status_reissue'),
            'cancel' => $this->translate('cert_status_cancel'),
        ];
    }

    /**
     * Return array with the download types
     * 
     * @return array
     */
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

    /**
     * Return array with the certificate index info
     * 
     * @return array
     */
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
            $status = $this->getValidationStatus();
            $certificate['displayStatus'] = isset($status[$crtStatus]) ? $status[$crtStatus] : $crtStatus;

            if (empty($infoResponse->get('sslKey'))) {
                unset($downloadTypes['pfx']);
            }
        }

        return $this->send('templates/overview.tpl', [
            'certificate' => $certificate,
            'dd_product_name' => $ddProductName,
            'download_types' => $downloadTypes,
            'in_process' => $crtStatus === 'process',
            'is_valid' => $crtStatus === 'valid',
            'product_is_pending' => $this->getApp()->getParams()['status'] === 'Pending',
            'links' => [
                'download_crt' => $this->buildUrl(static::ACTION_DOWNLOAD_CRT),
                'viewreissue' => $this->buildUrl(static::VIEW_REISSUE),
                'validation' => $this->buildUrl(static::VIEW_VALIDATION),
                'domain_mails' => $this->buildUrl(static::ACTION_GET_DOMAIN_MAILS),
            ]
        ]);
    }

    /**
     * Return array with the validation view info
     * 
     * @return array
     */
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
                'domain_mails' => $this->buildUrl(static::ACTION_GET_DOMAIN_MAILS),
            ]
        ]);
    }

    /**
     * Return array with the reissue view info
     * 
     * @return array
     */
    protected function view_Reissue(): array
    {
        $getInfoResponse = $this->getApp()->getCertificateInfo('validationStatus');
        $user = $this->getApp()->getParams()['clientsdetails'];
        $validationMethods = $this->getValidationMethods();
        unset($validationMethods['mail']);
        $certificate = [];
        $mails = [];
        $alternativeNames = [];
        $maxDomains = 0;

        if (is_object($getInfoResponse)) {
            $certificate = $getInfoResponse->getResponseData();
            $canReissue = $getInfoResponse->get('status') === 'valid';
            $alternativeNames = $getInfoResponse->get('alternativeNames');
            $maxDomains = $getInfoResponse->get('numDomains') - 1;

            if (!$canReissue) {
                $this->setErrorMsg($this->translate('cert_can_not_reissue'));
            }

            $mails = $this->getApp()->getCommonNameValidationEmails($getInfoResponse->get('commonName'));

            foreach ($alternativeNames as $key => $alts) {
                if ($alts === $getInfoResponse->get('commonName')) {
                    unset($alternativeNames[$key]);
                }
            }
        }

        return $this->send('templates/reissue.tpl', [
            'user' => $user,
            'certificate' => $certificate,
            'can_reissue' => $canReissue,
            'validation_methods' => $validationMethods,
            'validation_mails' => array_combine($mails, $mails),
            'default_method_title' => $this->translate('cert_validation_dns'),
            'alt_names' => array_values($alternativeNames),
            'max_domains' => $maxDomains,
            'links' => [
                'action_reissue' => $this->buildUrl(static::ACTION_REISSUE),
                'index' => $this->buildUrl(static::VIEW_INDEX),
                'domain_mails' => $this->buildUrl(static::ACTION_GET_DOMAIN_MAILS),
            ],
        ]);
    }

    /**
     * Reissue the certificate
     * 
     * @return void
     */
    protected function action_Reissue(): void
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
            'msg' => $isSuccess ? $this->translate('cert_reissue_success') : $reissueResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);
    }

    /**
     * Change a common name validation method of the certificate
     * 
     * @return void
     */
    protected function action_ChangeMethod(): void
    {
        $commonName = $this->getRequest()->getParam('common_name', '');
        $method = $this->getRequest()->getParam('validation_method', '');

        $changeMethodResponse = $this->getApp()->changeValidationMethod($commonName, $method);
        $isSuccess = $changeMethodResponse === 'success';

        $response = [
            'success' => $isSuccess,
            'msg' => $isSuccess ? $this->translate('cert_change_method_success') : $changeMethodResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);
    }

    /**
     * Resend a common name validation mail of the certificate
     * 
     * @return void
     */
    protected function action_ResendValidationMail(): void
    {
        $commonName = $this->getRequest()->getParam('common_name', '');
        $resendResponse = $this->getApp()->resendValidationMail($commonName);
        $isSuccess = $resendResponse === 'success';

        $response = [
            'success' => $isSuccess,
            'msg' => $isSuccess ? $this->translate('cert_resend_mail_success') : $resendResponse,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);
    }

    /**
     * Download the Certificate
     * 
     * @return array if can't download return the index
     */
    protected function action_DownloadCRT(): array
    {
        $infoType = $this->getRequest()->getParam('type', 'zip');
        $pass = $this->getRequest()->getParam('password', '');

        $response = $this->getApp()->getCertificateInfo($infoType, $pass);

        if (empty($response) || !is_array($response->get('content'))) {
            $this->setErrorMsg($this->translate('cert_download_fail'));
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

    /**
     * Return JSON with the common name validation mails
     * 
     * @return void
     */
    protected function action_GetDomainMails(): void
    {
        $commonName = $this->getRequest()->getParam('common_name', '');
        $mails = $this->getApp()->getCommonNameValidationEmails($commonName);

        $response = [
            'success' => !is_null($mails),
            'mails' => $mails,
        ];

        $this->getResponse()->setContentType(\WHMCS\Module\Addon\Dondominio\Helpers\Response::CONTENT_JSON);
        $this->getResponse()->send(json_encode($response), true);
    }
}
