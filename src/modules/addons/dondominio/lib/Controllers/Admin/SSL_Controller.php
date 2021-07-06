<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\App;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class SSL_Controller extends Controller
{
    const CONTROLLER_NAME = 'ssl';
    const DEFAULT_TEMPLATE_FOLDER = 'ssl';

    const VIEW_INDEX = '';
    const VIEW_AVAILABLE_SSL = 'availablessl';
    const VIEW_EDIT_PRODUCT = 'editproduct';
    const VIEW_WHMCS_PRODUCTS = 'whmcsproducts';
    const VIEW_CERTIFICATE_INFO = 'certificateinfo';
    const VIEW_RENEW = 'viewrenew';
    const ACTION_RENEW = 'actionrenew';
    const VIEW_REISSUE = 'viewreissue';
    const ACTION_REISSUE = 'actionreissue';
    const VIEW_CHANGE_VALIDATION_METHOD = 'viewchangevalidationmethod';
    const ACTION_CHANGE_VALIDATION_METHOD = 'actionchangevalidationmethod';
    const ACTION_RESEND_VALIDATION_MAIL = 'resendvalidationmail';
    const VIEW_SYNC = 'sync';
    const ACTION_SYNC = 'actionsync';
    const ACTION_UPDATEPRODUCT = 'updateproduct';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_AVAILABLE_SSL => 'view_AvailableSSL',
            static::VIEW_EDIT_PRODUCT => 'view_EditProduct',
            static::VIEW_WHMCS_PRODUCTS => 'view_WhmcsProducts',
            static::VIEW_CERTIFICATE_INFO => 'view_CertificateInfo',
            static::VIEW_RENEW => 'view_Renew',
            static::ACTION_RENEW => 'action_Renew',
            static::VIEW_REISSUE => 'view_Reissue',
            static::ACTION_REISSUE => 'action_Reissue',
            static::VIEW_CHANGE_VALIDATION_METHOD => 'view_ChangeValidationMethod',
            static::ACTION_CHANGE_VALIDATION_METHOD => 'action_ChangeValidationMethod',
            static::ACTION_RESEND_VALIDATION_MAIL => 'action_ResendValidationMail',
            static::VIEW_SYNC => 'view_Sync',
            static::ACTION_SYNC => 'action_Sync',
            static::ACTION_UPDATEPRODUCT => 'action_UpdateProduct',
        ];
    }

    /**
     * View for SSL Certificates list
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $app = $this->getApp();
        $whmcsService = $app->getService('whmcs');
        $apiService = $app->getService('api');
        $sslService = $app->getSSLService();

        $page = $this->getRequest()->getParam('page', 1);
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');
        $filters = [
            'status' => $this->getRequest()->getParam('certificate_status'),
            'renewable' => $this->getRequest()->getParam('certificate_renewable'),
            'commonName' => $this->getRequest()->getParam('certificate_common_name'),
        ];
        $filters['renewable'] = strlen($filters['renewable']) ? $filters['renewable'] : null;

        $totalRecords = 0;
        $certificatesData = null;

        try {
            $certificates = $apiService->getSSLCertificates($page, $limit, $filters);
            $totalRecords = $certificates->get('queryInfo')['total'];
            $certificatesData =  $certificates->getResponseData()['ssl'];
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $status = [
            'process' => $app->getLang('ssl_certificate_status_process'),
            'valid' => $app->getLang('ssl_certificate_status_valid'),
            'expired' => $app->getLang('ssl_certificate_status_expired'),
            'renew' => $app->getLang('ssl_certificate_status_renew'),
            'reissue' => $app->getLang('ssl_certificate_status_reissue'),
            'cancel' => $app->getLang('ssl_certificate_status_cancel'),
        ];

        $renewable = [
            true => $app->getLang('ssl_certificate_renew_true'),
            false => $app->getLang('ssl_certificate_renew_false'),
        ];

        $products = [];
        foreach ($whmcsService->getSSLProducts() as $product) {
            $products[$product->dd_product_id] = $product->product_name;
        }

        foreach ($certificatesData as $key => $cert) {
            $statusKey = $cert['status'];
            $productID = $cert['productID'];
            $certificateOrder = $sslService->getCertificateOrder($cert['certificateID']);

            $certificatesData[$key]['displayStatus'] = isset($status[$statusKey]) ? $status[$statusKey] : $statusKey;
            $certificatesData[$key]['productName'] = isset($products[$productID]) ? $products[$productID] : $productID;
            $certificatesData[$key]['order_id'] = is_object($certificateOrder) ? $certificateOrder->tblhosting_id : null;
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'certificates' => $certificatesData,
            'certificates_status' => $status,
            'certificates_renewable' => $renewable,
            'actions' => [
                'view_certificates' => static::VIEW_INDEX,
            ],
            'filters' => $filters,
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page - 1)], $filters)),
                'next_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page + 1)], $filters)),
                'view_certificate' => static::makeUrl(static::VIEW_CERTIFICATE_INFO, ['certificate_id' => '']),
                'whmcs_order' => 'clientsservices.php?id=',
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_INDEX);

        return $this->view('certificates', $params);
    }

    /**
     * View for WHMCS products created by DonDominio Module list
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_WhmcsProducts()
    {
        $app = $this->getApp();
        $whmcsService = $app->getService('whmcs');

        $filters = [
            'whmcs_product_name' => $this->getRequest()->getParam('whmcs_product_name'),
            'product_imported' => true,
        ];

        $page = $this->getRequest()->getParam('page', 1);
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');
        $offset = (($page - 1) * $limit);

        $products = $whmcsService->getSSLProducts($filters, $offset, $limit);
        $totalRecords = $whmcsService->getSSLProductsTotal($filters);

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'products' => $products,
            'validation_types' => \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::getValidationTypes(),
            'actions' => [
                'view_index' => static::VIEW_WHMCS_PRODUCTS,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_WHMCS_PRODUCTS, array_merge(['page' => ($page - 1)])),
                'next_page' => static::makeUrl(static::VIEW_WHMCS_PRODUCTS, array_merge(['page' => ($page + 1)])),
                'create_whmcs_product' => static::makeURL(static::VIEW_EDIT_PRODUCT, ['productid' => '']),
            ],
            'filters' => $filters,
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_WHMCS_PRODUCTS);

        return $this->view('index', $params);
    }

    /**
     * Retrieves template for availables SSL Products view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_AvailableSSL()
    {
        $app = $this->getApp();
        $whmcsService = $app->getService('whmcs');

        $filters = [
            'product_name' => $this->getRequest()->getParam('product_name'),
            'product_multi_domain' => $this->getRequest()->getParam('product_multi_domain'),
            'product_wildcard' => $this->getRequest()->getParam('product_wildcard'),
            'product_trial' => $this->getRequest()->getParam('product_trial'),
            'product_validation_type' =>  $this->getRequest()->getParam('product_validation_type'),
            'product_imported' => false,
        ];

        $page = $this->getRequest()->getParam('page', 1);
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');
        $offset = (($page - 1) * $limit);

        $products = $whmcsService->getSSLProducts($filters, $offset, $limit);
        $totalRecords = $whmcsService->getSSLProductsTotal($filters);

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'products' => $products,
            'validation_types' => \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::getValidationTypes(),
            'actions' => [
                'view_availabel_ssl' => static::VIEW_AVAILABLE_SSL,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_AVAILABLE_SSL, array_merge(['page' => ($page - 1)])),
                'next_page' => static::makeUrl(static::VIEW_AVAILABLE_SSL, array_merge(['page' => ($page + 1)])),
                'create_whmcs_product' => static::makeURL(static::VIEW_EDIT_PRODUCT, ['productid' => '']),
            ],
            'filters' => $filters,
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_AVAILABLE_SSL);

        return $this->view('availableSSL', $params);
    }

    /**
     * View for SSL Certificate info
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_CertificateInfo()
    {
        $app = $this->getApp();
        $sslService = $app->getSSLService();
        $apiService = $app->getService('api');

        $status = \WHMCS\Module\Addon\Dondominio\Models\SSLCertificateOrder_Model::getStatus();
        $certificateID = $this->getRequest()->getParam('certificate_id');
        $certificateOrder = $sslService->getCertificateOrder($certificateID);
        $product = null;
        $whmcsProduct = null;
        $service = null;
        $user = null;
        $certificatesData = [];
        $certificateStatus = '';

        if (is_object($certificateOrder)) {
            $service = $certificateOrder->getService();
            $user = $service->client;
            $whmcsProduct = $service->product;
        }

        try {
            $certificates = $apiService->getSSLCertificateInfo($certificateID);
            $certificatesData =  $certificates->getResponseData();
            $product = $sslService->getProduct($certificatesData['productID']);
            $certificateStatus = $certificatesData['status'];
            $certificatesData['displayStatus'] = isset($status[$certificateStatus]) ? $status[$certificateStatus] : $certificateStatus;
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            'certificate' => $certificatesData,
            'service' => $service,
            'user' => $user,
            'whmcs_product' => $whmcsProduct,
            'product' => $product,
            'in_process' => $certificateStatus === \WHMCS\Module\Addon\Dondominio\Models\SSLCertificateOrder_Model::STATUS_PROCESS,
            'in_reissue' => $certificateStatus === \WHMCS\Module\Addon\Dondominio\Models\SSLCertificateOrder_Model::STATUS_REISSUE,
            'links' => [
                'view_renew' => static::makeUrl(static::VIEW_RENEW, ['certificate_id' => $certificateID]),
                'view_reissue' => static::makeUrl(static::VIEW_REISSUE, ['certificate_id' => $certificateID]),
                'view_change_validation_method' => static::makeUrl(static::VIEW_CHANGE_VALIDATION_METHOD, ['certificate_id' => $certificateID]),
                'action_resend_validation_mail' => static::makeUrl(static::ACTION_RESEND_VALIDATION_MAIL, [
                    'certificate_id' => $certificateID,
                    'common_name' => $certificatesData['commonName']
                ]),
                'whmcs_order' => 'clientsservices.php?id=',
            ],
        ];

        return $this->view('certificateinfo', $params);
    }

    /**
     * View for renew SSL Certificate
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Renew()
    {
        $app = $this->getApp();
        $sslService = $app->getSSLService();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $certificateOrder = $sslService->getCertificateOrder($certificateID);
        $vatNumber = $this->getRequest()->getParam('contact_iden_num');
        $address = $this->getRequest()->getParam('contact_address');
        $certificateID = $this->getRequest()->getParam('certificate_id');
        $phone = $this->getRequest()->getParam('contact_phone');
        $phoneCode = $this->getRequest()->getParam('country-calling-code-contact_phone');
        $fax = $this->getRequest()->getParam('contact_fax');
        $faxCode = $this->getRequest()->getParam('country-calling-code-contact_fax');
        $csrOrgName = $this->getRequest()->getParam('organization_name');
        $csrOrgUnitName = $this->getRequest()->getParam('organization_unit_name');
        $csrCountry = $this->getRequest()->getParam('country_name');
        $csrState = $this->getRequest()->getParam('state_or_province_name');
        $csrLocation = $this->getRequest()->getParam('location_name');
        $csrEmailAddress = $this->getRequest()->getParam('email_address');
        $contactID = $this->getRequest()->getParam('contact_id');
        $contactType = $this->getRequest()->getParam('contact_type', 'individual');
        $user = [
            'firstname' => $this->getRequest()->getParam('contact_first_name'),
            'lastname' => $this->getRequest()->getParam('contact_last_name'),
            'companyname' => $this->getRequest()->getParam('contact_org_name'),
            'unitname' => $this->getRequest()->getParam('organization_unit_name'),
            'email' => $this->getRequest()->getParam('contact_email'),
            'phonenumberformatted' => strlen($phone) ? sprintf('+%s.%s', $phoneCode, $phone) : '',
            'fax' => strlen($fax) ? sprintf('+%s.%s', $faxCode, $fax) : '',
            'postcode' => $this->getRequest()->getParam('contact_post_code'),
            'city' => $this->getRequest()->getParam('contact_city'),
            'state' => $this->getRequest()->getParam('contact_state'),
            'countrycode' => $this->getRequest()->getParam('contact_country'),
        ];
        $certificatesData = [];

        if (is_object($certificateOrder)) {
            $user = $certificateOrder->getClientDetails();
            $vatNumber = $certificateOrder->getVatNumber();
            $address = strlen($user['address1']) ? $user['address1'] : $user['address2'];
            $csrOrgName = strlen($csrOrgName) ? $csrOrgName : $user['companyname'];
            $csrCountry = strlen($csrCountry) ? $csrCountry : $user['countrycode'];
            $csrState = strlen($csrState) ? $csrState : $user['state'];
            $csrLocation = strlen($csrLocation) ? $csrLocation : $user['city'];
            $csrEmailAddress = strlen($csrEmailAddress) ? $csrEmailAddress : $user['email'];
        }

        $contactTypes = [
            'individual' => $app->getLang('ssl_contact_individual'),
            'organization' => $app->getLang('ssl_contact_organization')
        ];

        try {
            $certificates = $apiService->getSSLCertificateInfo($certificateID);
            $certificatesData =  $certificates->getResponseData();
        } catch (\Exception $e) {
            $this->getResponse()->addError($app->getLang($e->getMessage()));
        }

        print_r($contactType);

        $params = [
            'module_name' => $app->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'certificate' => $certificatesData,
            'user' => $user,
            'contact_types' => $contactTypes,
            'vat_number' => $vatNumber,
            'address' => $address,
            'csr_org_name' => $csrOrgName,
            'csr_org_unit_name' => $csrOrgUnitName,
            'csr_country' => $csrCountry,
            'csr_state' => $csrState,
            'csr_location' => $csrLocation,
            'csr_email_address' => $csrEmailAddress,
            'contact_id' => $contactID,
            'is_contact_data' => is_null($contactID),
            'contact_type' => $contactType,
            'actions' => [
                'renew' => static::ACTION_RENEW
            ],
            'links' => [
                'view_certificateinfo' =>  static::makeUrl(static::VIEW_CERTIFICATE_INFO, ['certificate_id' => $certificateID]),
            ]
        ];

        return $this->view('renew', $params);
    }

    /**
     * Action for renew SSL Certificate
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Renew()
    {
        $app = $this->getApp();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $phone = $this->getRequest()->getParam('contact_phone');
        $phoneCode = $this->getRequest()->getParam('country-calling-code-contact_phone');
        $fax = $this->getRequest()->getParam('contact_fax');
        $faxCode = $this->getRequest()->getParam('country-calling-code-contact_fax');

        $CSRArgs = [
            'commonName' => $this->getRequest()->getParam('common_name'),
            'organizationName' => $this->getRequest()->getParam('organization_name'),
            'organizationalUnitName' => $this->getRequest()->getParam('organization_unit_name'),
            'countryName' => $this->getRequest()->getParam('country_name'),
            'stateOrProvinceName' => $this->getRequest()->getParam('state_or_province_name'),
            'localityName' => $this->getRequest()->getParam('location_name'),
            'emailAddress' => $this->getRequest()->getParam('email_address'),
        ];
        $renewArgs = [
            'adminContactID' => $this->getRequest()->getParam('contact_id'),
            'adminContactType' => $this->getRequest()->getParam('contact_type'),
            'adminContactFirstName' => $this->getRequest()->getParam('contact_first_name'),
            'adminContactLastName' => $this->getRequest()->getParam('contact_last_name'),
            'adminContactOrgName' => $this->getRequest()->getParam('contact_org_name'),
            'adminContactOrgType' => $this->getRequest()->getParam('contact_org_type'),
            'adminContactIdentNumber' => $this->getRequest()->getParam('contact_iden_num'),
            'adminContactEmail' => $this->getRequest()->getParam('contact_email'),
            'adminContactPhone' => sprintf('+%s.%s', $phoneCode, $phone),
            'adminContactFax' => strlen($fax) ? sprintf('+%s.%s', $faxCode, $fax) : '',
            'adminContactAddress' => $this->getRequest()->getParam('contact_address'),
            'adminContactPostalCode' => $this->getRequest()->getParam('contact_post_code'),
            'adminContactCity' => $this->getRequest()->getParam('contact_city'),
            'adminContactState' => $this->getRequest()->getParam('contact_state'),
            'adminContactCountry' => $this->getRequest()->getParam('contact_country'),
        ];

        try {
            throw new \Exception('test');
            $csrResponse = $apiService->createCSRData($CSRArgs);

            $renewArgs['csrData'] = $csrResponse->get('csrData');
            $renewArgs['keyData'] = $csrResponse->get('csrKey');


            $apiService->renewCertificate($certificateID, $renewArgs);
            $this->getResponse()->addSuccess($app->getLang('ssl_success_renew'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
            return $this->view_Renew();
        }

        return $this->view_CertificateInfo();
    }

    /**
     * View for reissue SSL Certificate
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Reissue()
    {
        $app = $this->getApp();
        $sslService = $app->getSSLService();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $certificateOrder = $sslService->getCertificateOrder($certificateID);
        $user = [];
        $certificatesData = [];

        if (is_object($certificateOrder)) {
            $user = $certificateOrder->getClientDetails();
        }

        try {
            $certificates = $apiService->getSSLCertificateInfo($certificateID);
            $certificatesData =  $certificates->getResponseData();
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'certificate' => $certificatesData,
            'user' => $user,
            'actions' => [
                'reissue' => static::ACTION_REISSUE
            ],
            'links' => [
                'view_certificateinfo' =>  static::makeUrl(static::VIEW_CERTIFICATE_INFO, ['certificate_id' => $certificateID]),
            ]
        ];

        return $this->view('reissue', $params);
    }

    /**
     * Action for reissue SSL Certificate
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Reissue()
    {
        $app = $this->getApp();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $CSRArgs = [
            'commonName' => $this->getRequest()->getParam('common_name'),
            'organizationName' => $this->getRequest()->getParam('organization_name'),
            'organizationalUnitName' => $this->getRequest()->getParam('organization_unit_name'),
            'countryName' => $this->getRequest()->getParam('country_name'),
            'stateOrProvinceName' => $this->getRequest()->getParam('state_or_province_name'),
            'localityName' => $this->getRequest()->getParam('location_name'),
            'emailAddress' => $this->getRequest()->getParam('email_address'),
        ];

        try {
            $csrResponse = $apiService->createCSRData($CSRArgs);

            $reissueArgs = [
                'csrData' => $csrResponse->get('csrData'),
                'keyData' => $csrResponse->get('csrKey'),
            ];

            $apiService->reissueCertificate($certificateID, $reissueArgs);
            $this->getResponse()->addSuccess($app->getLang('ssl_success_reissue'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
            return $this->view_Reissue();
        }

        return $this->view_CertificateInfo();
    }

    /**
     * View for change SSL Certificate validation method
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_ChangeValidationMethod()
    {
        $app = $this->getApp();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $domain = [];
        $validationMethods = [
            'mail' => 'Validación por correo electrónico',
            'dns' => 'Validación mediante registro dns en la zona dns del dominio',
            'http' => 'Validación mediante protocolo http',
            'https' => 'Validación mediante protocolo https',
        ];

        try {
            $certificates = $apiService->getSSLCertificateInfo($certificateID, 'validationStatus');
            $certificatesData = $certificates->getResponseData();

            $dcv = $certificatesData['validationData']['dcv'];

            foreach ($dcv as $key => $val) {
                $domain[$key] = [$key];
            }
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'certificate' => $certificatesData,
            'domains' => $domain,
            'validation_methods' => $validationMethods,
            'actions' => [
                'change_validaton_method' => static::ACTION_CHANGE_VALIDATION_METHOD
            ],
            'links' => [
                'view_certificateinfo' =>  static::makeUrl(static::VIEW_CERTIFICATE_INFO, ['certificate_id' => $certificateID]),
            ]
        ];

        return $this->view('changevalidatonmethod', $params);
    }

    /**
     * Action for change SSL Certificate validation method
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_ChangeValidationMethod()
    {
        $app = $this->getApp();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $commonName = $this->getRequest()->getParam('common_name');
        $validationMethod = $this->getRequest()->getParam('validation_method');

        try {
            $apiService->changeValidationName($certificateID, $commonName, $validationMethod);
            $this->getResponse()->addSuccess($app->getLang('ssl_success_change_validation_method'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
            return $this->view_ChangeValidationMethod();
        }

        return $this->view_CertificateInfo();
    }

    /**
     * Action for resend the validation mail of a CommonName of a Certificate.
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_ResendValidationMail()
    {
        $app = $this->getApp();
        $apiService = $app->getService('api');

        $certificateID = $this->getRequest()->getParam('certificate_id');
        $commonName = $this->getRequest()->getParam('common_name');

        try {
            $apiService->resendValidationMail($certificateID, $commonName);
            $this->getResponse()->addSuccess($app->getLang('ssl_success_resend_validation_mail'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_CertificateInfo();
    }

    /**
     * Action for sync WHMCS Products with DonDominio Products
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Sync()
    {
        $app = $this->getApp();
        $updatePrices = (bool) $this->getRequest()->getParam('update_prices', false);

        try {
            $app->getSSLService()->apiSync($updatePrices);
            $this->getResponse()->addSuccess($app->getLang('ssl_sync_success'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($app->getLang($e->getMessage()));

            return $this->view_Sync();
        }

        return $this->view_Index();
    }

    /**
     * View to sync Products
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Sync()
    {
        $settings = $this->getApp()->getService('settings')->findSettingsAsKeyValue();
        $this->setActualView(static::VIEW_SYNC);

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'update_prices' => $settings->get('prices_autoupdate') == '1' ? "checked='checked'" : "",
            'actions' => [
                'sync' => static::ACTION_SYNC
            ],
        ];

        return $this->view('sync', $params);
    }

    /**
     * View to create/edit products in WHMCS
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_EditProduct()
    {
        $sslService = $this->getApp()->getSSLService();
        $whmcsService = $this->getApp()->getService('whmcs');
        $id = $this->getRequest()->getParam('productid', 0);
        $product = $sslService->getProduct($id);

        $whmcsProductGroups = $sslService->getProductGroups();
        $whmcsProduct = $product->getWhmcsProduct();
        $productName = $this->getRequest()->getParam('name', $product->product_name);
        $productGroup = $this->getRequest()->getParam('group', '');
        $vatNumber = $whmcsService->getVatNumberID();

        if (is_object($whmcsProduct)) {
            $productName = $whmcsProduct->name;
            $productGroup = $whmcsProduct->gid;
            $vatNumber = $whmcsProduct->configoption2;
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'product' => $product,
            'product_name' => $productName,
            'product_group' => $productGroup,
            'groups' => $whmcsProductGroups,
            'increment_type' => $product->price_create_increment_type,
            'has_whmcs_product' => $product->hasWhmcsProduct(),
            'client_custom_field' => $whmcsService->getCustomClientFields(),
            'vat_number_id' => $vatNumber,
            'links' => [
                'ssl_index' => static::makeURL(static::VIEW_INDEX),
                'create_group' => 'configproducts.php?action=creategroup',
                'whmcs_product_edit' => sprintf('configproducts.php?action=edit&id=%d', $product->tblproducts_id),
            ],
            'actions' => [
                'update_product' => static::ACTION_UPDATEPRODUCT,
            ],
        ];

        return $this->view('editproduct', $params);
    }

    /**
     * Action to create/edit products in WHMCS
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_UpdateProduct()
    {
        $app = $this->getApp();
        $sslService = $app->getSSLService();

        $id = $this->getRequest()->getParam('productid', 0);
        $group = $this->getRequest()->getParam('group', 0);
        $name = $this->getRequest()->getParam('name', '');
        $increment = $this->getRequest()->getParam('increment', 0);
        $vatNumber = $this->getRequest()->getParam('vat_number', 0);
        $incrementType = $this->getRequest()->getParam('increment_type', '');

        $product = $sslService->getProduct($id);
        $product->price_create_increment = $increment;
        $product->price_create_increment_type = $incrementType;

        try {
            $product->updateWhmcsProduct($group, $name, $vatNumber);
            $product->save();
            $this->getResponse()->addSuccess($app->getLang('ssl_product_create_succesful'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($app->getLang($e->getMessage()));
            return $this->view_EditProduct();
        }

        return $this->view_WhmcsProducts();
    }

    /**
     * Searchs and returns a template
     * 
     * @param string $view View in format "folder.file" or "file"
     * @param array $params Params to pass to template
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view($view, array $params = [])
    {
        $app = App::getInstance();

        $params['title'] = $app->getLang('content_title_ssl');
        $params['nav'] = [
            [
                'title' => $app->getLang('ssl_certificates'),
                'link' => static::makeURL(static::VIEW_INDEX),
                'selected' => $this->checkActualView(static::VIEW_INDEX)
            ],
            [
                'title' => $app->getLang('ssl_products'),
                'link' => static::makeURL(static::VIEW_WHMCS_PRODUCTS),
                'selected' => $this->checkActualView(static::VIEW_WHMCS_PRODUCTS)
            ],
            [
                'title' => $app->getLang('ssl_available_products'),
                'link' => static::makeURL(static::VIEW_AVAILABLE_SSL),
                'selected' => $this->checkActualView(static::VIEW_AVAILABLE_SSL)
            ],
            [
                'title' => $app->getLang('ssl_sync'),
                'link' => static::makeURL(static::VIEW_SYNC),
                'selected' => $this->checkActualView(static::VIEW_SYNC)
            ],
        ];

        return parent::view($view, $params);
    }
}
