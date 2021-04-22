<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\Models\TldSettings_Model;
use Exception;
use WHMCS\Module\Addon\Dondominio\App;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class DomainPricings_Controller extends Controller
{
    const CONTROLLER_NAME = 'domainpricings';
    const DEFAULT_TEMPLATE_FOLDER = 'domainpricings';

    const VIEW_INDEX = '';
    const VIEW_AVAILABLE_TLDS = 'viewavailabletlds';
    const VIEW_SETTINGS = 'viewsettings';

    const ACTION_UPDATE_PRICES = 'updateprices';
    const ACTION_SWITCH_REGISTRAR = 'switchregistrar';
    const ACTION_CREATE = 'create';
    const ACTION_REORDER = 'reorder';
    const ACTION_SAVE_SETTINGS = 'savesettings';
    const ACTION_SYNC_TLDS = 'synctlds';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_AVAILABLE_TLDS => 'view_AvailableTlds',
            static::VIEW_SETTINGS => 'view_Settings',
            static::ACTION_UPDATE_PRICES => 'action_UpdatePrices',
            static::ACTION_SWITCH_REGISTRAR => 'action_SwitchRegistrar',
            static::ACTION_CREATE => 'action_Create',
            static::ACTION_REORDER => 'action_Reorder',
            static::ACTION_SAVE_SETTINGS => 'action_SaveSettings',
            static::ACTION_SYNC_TLDS => 'action_SyncTLDs',
        ];
    }

    /**
     * View for index (price update)
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $app = $this->getApp();
        $whmcsService = $app->getService('whmcs');
        $page = $this->getRequest()->getParam('page', 1);
        $tld = $this->getRequest()->getParam('tld', '');
        $registrar = $this->getRequest()->getParam('registrar', '');

        $filters = [
            'tld' => $tld,
            'autoreg' => $registrar,
        ];

        // GET TLDS BY PAGINATION

        $totalTlds = $whmcsService->getDomainPricingsCount($tld);
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');
        $offset = (($page - 1) * $limit);

        $tlds = $whmcsService->getDomainPricings($filters, $offset, $limit);
        $registrars = $whmcsService->getDisctintRegistrars()->toArray();

        // PARAMS TO TEMPLATE

        $params = [
            'module_name' => $app->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'local_tlds' => $tlds,
            'filters' => $filters,
            'registrars' => $registrars,
            'actions' => [
                'index' => static::VIEW_INDEX,
                'update_prices' => static::ACTION_UPDATE_PRICES,
                'switch_registrar' => static::ACTION_SWITCH_REGISTRAR,
                'reorder_tlds' => static::ACTION_REORDER,
            ],
            'links' => [
                'view_settings' => static::makeURL(static::VIEW_SETTINGS, ['tld' => '']),
                'prev_page' => static::makeUrl(static::VIEW_INDEX, ['page' => ($page - 1)] + $filters),
                'next_page' => static::makeUrl(static::VIEW_INDEX, ['page' => ($page + 1)] + $filters)
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalTlds);
        $this->setActualView(static::VIEW_INDEX);

        return $this->view('index', $params);
    }

    /**
     * View for available TLDs
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_AvailableTlds()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $filters = [
            'tld' => $this->getRequest()->getParam('tld')
        ];

        // GET TLDS BY PAGINATION

        $availableTldsCount = $this->getApp()->getService('pricing')->getAvailableTldsCount($filters);
        $limit = $this->getApp()->getService('whmcs')->getConfiguration('NumRecordstoDisplay');

        $total_pages = ceil($availableTldsCount / $limit);

        $total_pages = (int) $total_pages === 0 ? 1 : $total_pages;
        $page = $page > $total_pages ? $total_pages : $page;
        $offset = (($page - 1) * $limit);

        $availableTlds = $this->getApp()->getService('pricing')->getAvailableTlds($filters, $offset, $limit);

        // PARAMS TO TEMPLATE

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'filters' => $filters,
            'tlds' => $availableTlds,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $availableTldsCount,
                'total_pages' => $total_pages
            ],
            'actions' => [
                'availables' => static::VIEW_AVAILABLE_TLDS,
                'create' => static::ACTION_CREATE
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_AVAILABLE_TLDS, array_merge($filters, ['page' => ($page - 1)])),
                'next_page' => static::makeUrl(static::VIEW_AVAILABLE_TLDS, array_merge($filters, ['page' => ($page + 1)])),
            ],
        ];

        $this->setPagination($params, $limit, $page, $availableTldsCount);
        $this->setActualView(static::VIEW_AVAILABLE_TLDS);

        return $this->view('availables', $params);
    }

    /**
     * View for TLD Settings
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Settings()
    {
        $tld = $this->getRequest()->getParam('tld');

        $tldSettings = $this->getApp()->getService('tld_settings')->getTldSettingsByTld($tld);

        if (is_null($tldSettings)) {
            $tldSettings = new TldSettings_Model();
            $tldSettings->tld = $tld;
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'tld' => $tld,
            'tld_settings' => $tldSettings,
            'actions' => [
                'save_settings' => static::ACTION_SAVE_SETTINGS
            ],
            'links' => [
                'tlds_index' => static::makeURL()
            ],
        ];

        return $this->view('settings', $params);
    }

    /**
     * Action for updating prices
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_UpdatePrices()
    {
        try {
            $extensions = $this->getRequest()->getArrayParam('tld');

            $this->getApp()->getService('whmcs')->insertPricingsForOtherCurrencies();

            if (empty($extensions)) {
                throw new Exception('tld_no_selected');
            }

            foreach ($extensions as $extension) {
                $tld = $this->getApp()->getService('pricing')->findPricingByTld($extension);

                try {
                    if (is_null($tld)) {
                        throw new Exception('domains_tld_not_valid');
                    }

                    $this->getApp()->getService('whmcs')->savePricingsForEur($tld);

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $tld->tld, $this->getApp()->getLang('tld_updated_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', (!is_null($tld) ? $tld->tld : $extension), $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }

            //  Update currency exchange between different currencies
            $affectedRows = $this->getApp()->getService('whmcs')->updatePricingsForOtherCurrencies();
            $this->getResponse()->addInfo($affectedRows . ' ' . $this->getApp()->getLang('prices_updated'));

            // Update domains prices
            $affectedRows = $this->getApp()->getService('whmcs')->updateDomainPrices();
            $this->getResponse()->addInfo($affectedRows . ' ' . $this->getApp()->getLang('domains_updated'));
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Action for switch registrar in TLDs to dondominio
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SwitchRegistrar()
    {
        try {
            $extensions = $this->getRequest()->getArrayParam('tld');

            if (empty($extensions)) {
                throw new Exception('tld_no_selected');
            }

            foreach ($extensions as $extension) {
                try {
                    $this->getApp()->getService('whmcs')->updateTldRegistrar($extension, 'dondominio');

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $extension, $this->getApp()->getLang('tld_updated_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', $extension, $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Action for reorder TLDs
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Reorder()
    {
        try {
            $this->getApp()->getService('whmcs')->reorderTlds();

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Action for create TLD
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Create()
    {
        try {
            $tlds = $this->getRequest()->getArrayParam('tlds');

            if (empty($tlds)) {
                throw new Exception('tld_no_selected');
            }

            foreach ($tlds as $extension) {
                $pricing = $this->getApp()->getService('pricing')->findPricingByTld($extension);

                try {
                    if (is_null($pricing)) {
                        throw new Exception('tld_not_found');
                    }

                    $this->getApp()->getService('whmcs')->insertDomainPricing($pricing);
                    $this->getApp()->getService('whmcs')->savePricingsForEur($pricing);

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $pricing->tld, $this->getApp()->getLang('tld_added_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', $extension, $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }

            // Create pricings for other currencies
            $this->getApp()->getService('whmcs')->insertPricingsForOtherCurrencies();
            $this->getApp()->getService('whmcs')->updatePricingsForOtherCurrencies();
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_AvailableTlds();
    }

    /**
     * Action for save TLD settings
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SaveSettings()
    {
        $tld = $this->getRequest()->getParam('tld');

        $fields = [
            'tld' => $tld,
            'ignore' => $this->getRequest()->getParam('no_update') == 'on' ? 1 : 0,
            'enabled' => $this->getRequest()->getParam('status') == 'on' ? 1 : 0,
            'register_increase' => $this->getRequest()->getParam('registration'),
            'register_increase_type' => $this->getRequest()->getParam('registration_type'),
            'renew_increase' => $this->getRequest()->getParam('renewal'),
            'renew_increase_type' => $this->getRequest()->getParam('renewal_type'),
            'transfer_increase' => $this->getRequest()->getParam('transfer'),
            'transfer_increase_type' => $this->getRequest()->getParam('transfer_type')
        ];

        try {
            $this->getApp()->getService('tld_settings')->saveTld($tld, $fields);

            $this->getResponse()->setForceSuccess(true);
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Settings();
    }

    /**
     * Action for sync TLDs with the API
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SyncTLDs()
    {
        $app = $this->getApp();

        try {
            $this->getApp()->getService('pricing')->apiSync(false);
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        if ((int) $app->getService('settings')->getSetting('prices_autoupdate') !== 0) {
            $app->getService('pricing')->updateDomainPricing();
        }

        return $this->view_AvailableTlds();
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

        $params['title'] = $app->getLang('content_title_tld');
        $params['nav'] = [
            [
                'title' => $app->getLang('tld_title'),
                'link' => static::makeURL(static::VIEW_INDEX),
                'selected' => $this->checkActualView(static::VIEW_INDEX)
            ],
            [
                'title' => $app->getLang('tld_new_title'),
                'link' => static::makeURL(static::VIEW_AVAILABLE_TLDS),
                'selected' => $this->checkActualView(static::VIEW_AVAILABLE_TLDS)
            ],
            [
                'title' => $app->getLang('sync_tlds'),
                'link' => static::makeURL(static::ACTION_SYNC_TLDS),
                'selected' => false
            ],
        ];

        return parent::view($view, $params);
    }
}
