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
    const VIEW_CERTIFICATES = 'certificates';
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
            static::VIEW_CERTIFICATES => 'view_Certificates',
            static::VIEW_SYNC => 'view_Sync',
            static::ACTION_SYNC => 'action_Sync',
            static::ACTION_UPDATEPRODUCT => 'action_UpdateProduct',
        ];
    }

    public function view_Index()
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
                'view_index' => static::VIEW_INDEX,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page - 1)])),
                'next_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page + 1)])),
                'create_whmcs_product' => static::makeURL(static::VIEW_EDIT_PRODUCT, ['productid' => '']),
            ],
            'filters' => $filters,
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_INDEX);

        return $this->view('index', $params);
    }

    /**
     * Retrieves template for availables SSL Products view
     *
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
     * View for SSL Certificates list
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Certificates()
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

            $certificatesData[$key]['displayStatus'] = isset($status[$statusKey]) ? $status[$statusKey] : $statusKey;
            $certificatesData[$key]['productName'] = isset($products[$productID]) ? $products[$productID] : $productID;
            $certificatesData[$key]['order_id'] = $sslService->getCertificateOrderID($cert['certificateID']);
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'certificates' => $certificatesData,
            'certificates_status' => $status,
            'certificates_renewable' => $renewable,
            'actions' => [
                'view_certificates' => static::VIEW_CERTIFICATES,
            ],
            'filters' => $filters,
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_CERTIFICATES, array_merge(['page' => ($page - 1)], $filters)),
                'next_page' => static::makeUrl(static::VIEW_CERTIFICATES, array_merge(['page' => ($page + 1)], $filters)),
                'whmcs_order' => 'clientsservices.php?id=',
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_CERTIFICATES);

        return $this->view('certificates', $params);
    }

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

    public function view_EditProduct()
    {
        $sslService = $this->getApp()->getSSLService();
        $whmcsService = $this->getApp()->getService('whmcs');
        $id = $this->getRequest()->getParam('productid', 0);
        $product = $sslService->getProduct($id);

        $whmcsProductGroups = $sslService->getProductGroups();
        $whmcsProduct = $product->getWhmcsProduct();
        $productName = $this->getRequest()->getParam('name', '');
        $productGroup = $this->getRequest()->getParam('group', '');

        if (is_object($whmcsProduct)) {
            $productName = $whmcsProduct->name;
            $productGroup = $whmcsProduct->gid;
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
            'vat_number_id' => $whmcsService->getVatNumberID(),
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

        return $this->view_Index();
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
                'title' => $app->getLang('ssl_products'),
                'link' => static::makeURL(static::VIEW_INDEX),
                'selected' => $this->checkActualView(static::VIEW_INDEX)
            ],
            [
                'title' => $app->getLang('ssl_available_products'),
                'link' => static::makeURL(static::VIEW_AVAILABLE_SSL),
                'selected' => $this->checkActualView(static::VIEW_AVAILABLE_SSL)
            ],
            [
                'title' => $app->getLang('ssl_certificates'),
                'link' => static::makeURL(static::VIEW_CERTIFICATES),
                'selected' => $this->checkActualView(static::VIEW_CERTIFICATES)
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
