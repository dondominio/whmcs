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
    const VIEW_SYNC = 'sync';
    const ACTION_SYNC = 'viewsync';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_SYNC => 'view_Sync',
            static::ACTION_SYNC => 'action_Sync',
        ];
    }

    /**
     * Retrieves template for index view
     *
     */
    public function view_Index()
    {
        $app = $this->getApp();
        $whmcsService = $app->getService('whmcs');

        $filters = [
            'product_name' => $this->getRequest()->getParam('product_name'),
            'product_multi_domain' => $this->getRequest()->getParam('product_multi_domain'),
            'product_wildcard' => $this->getRequest()->getParam('product_wildcard'),
            'product_trial' => $this->getRequest()->getParam('product_trial'),
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
            'actions' => [
                'view_index' => static::VIEW_INDEX,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page - 1)])),
                'next_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page + 1)]))
            ],
            'filters' => $filters,
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_INDEX);

        return $this->view('index', $params);
    }

    public function action_Sync()
    {
        $app = $this->getApp();

        try {
            $app->getSSLService()->apiSync();
            $this->getResponse()->addSuccess($app->getLang('ssl_sync_success'));
        } catch (\Exception $e) {
            $this->getResponse()->addError($app->getLang($e->getMessage()));

            return $this->view_Index();
        }

        return $this->view_Index();
    }

    public function view_Sync()
    {
        $this->setActualView(static::VIEW_SYNC);

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'links' => [
                'sync' => static::makeURL(static::ACTION_SYNC)
            ],
        ];

        return $this->view('sync', $params);
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
                'title' => $app->getLang('ssl_sync'),
                'link' => static::makeURL(static::VIEW_SYNC),
                'selected' => $this->checkActualView(static::VIEW_SYNC)
            ],
        ];

        return parent::view($view, $params);
    }
}
