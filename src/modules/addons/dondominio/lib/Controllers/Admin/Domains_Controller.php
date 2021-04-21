<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use Exception;
use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Helpers\Response;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Domains_Controller extends Controller
{
    const CONTROLLER_NAME = 'domains';
    const DEFAULT_TEMPLATE_FOLDER = 'domains';

    const VIEW_INDEX = '';
    const VIEW_DOMAIN = 'viewdomain';
    const VIEW_TRANSFER = 'viewtransfer';
    const VIEW_IMPORT = 'viewimport';
    const VIEW_DELETED = 'viewdeleted';
    const VIEW_GETINFO = 'viewgetinfo';
    const VIEW_HISTORY = 'viewhistory';
    const VIEW_CONTACTS = 'viewcontacts';
    const VIEW_CONTACT = 'viewcontact';

    const ACTION_SYNC = 'sync';
    const ACTION_SWITCH_REGISTRAR = 'switchregistrar';
    const ACTION_UPDATE_PRICE = 'updateprice';
    const ACTION_UPDATE_CONTACT = 'updatecontact';
    const ACTION_TRANSFER = 'transfer';
    const ACTION_IMPORT = 'import';
    const action_RESENDVERIFICATIONMAIL = 'actionresendverificationmail';

    /**
     * Gets available actions for Controller
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index',
            static::VIEW_DOMAIN => 'view_Domain',
            static::VIEW_TRANSFER => 'view_Transfer',
            static::VIEW_IMPORT => 'view_Import',
            static::VIEW_DELETED => 'view_Deleted',
            static::VIEW_GETINFO => 'view_GetInfo',
            static::VIEW_HISTORY => 'view_History',
            static::VIEW_CONTACTS => 'view_Contacts',
            static::VIEW_CONTACT => 'view_Contact',
            static::ACTION_SYNC => 'action_Sync',
            static::ACTION_SWITCH_REGISTRAR => 'action_SwitchRegistrar',
            static::ACTION_UPDATE_PRICE => 'action_UpdatePrice',
            static::ACTION_UPDATE_CONTACT => 'action_updateContact',
            static::ACTION_TRANSFER => 'action_Transfer',
            static::ACTION_IMPORT => 'action_Import',
            static::action_RESENDVERIFICATIONMAIL => 'action_ResendVerificationMail',
        ];
    }

    /**
     * Retrieves template for index view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Index()
    {
        $filters = [
            'domain' => $this->getRequest()->getParam('domain'),
            'registrar' => $this->getRequest()->getParam('registrar'),
            'status' => $this->getRequest()->getParam('status'),
            'tld' => $this->getRequest()->getParam('tld'),
            'ddid' => $this->getRequest()->getParam('ddid')
        ];

        $page = $this->getRequest()->getParam('page', 1);

        // GET DOMAINS BY PAGINATION

        $whmcsService = $this->getApp()->getService('whmcs');

        $totalDomains = $whmcsService->getDomainsCount($filters);
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');

        $offset = (($page - 1) * $limit);

        $domains = $whmcsService->getDomains($filters, $offset, $limit);

        // PARAMS TO TEMPLATE

        $tlds = $whmcsService->getDomainPricingsForSelect();
        $registrars = $whmcsService->getDisctintRegistrars()->toArray();

        $statuses = [
            '' => $this->getApp()->getLang('filter_any'),
            'Pending' => $this->getApp()->getLang('filter_pending'),
            'Pending Transfer' => $this->getApp()->getLang('filter_pending_transfer'),
            'Active' => $this->getApp()->getLang('filter_active'),
            'Expired' => $this->getApp()->getLang('filter_expired'),
            'Cancelled' => $this->getApp()->getLang('filter_cancelled'),
            'Fraud' => $this->getApp()->getLang('filter_fraud')
        ];

        array_walk($statuses, function (&$status, $key) {
            $status = empty($status) ? $key : $status;
        });

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'filters' => $filters,
            'statuses' => $statuses,
            'tlds' => $tlds,
            'registrars' => $registrars,
            'domains' => $domains,
            'actions' => [
                'index' => static::VIEW_INDEX,
                'sync' => static::ACTION_SYNC,
                'switch_registrar' => static::ACTION_SWITCH_REGISTRAR,
                'update_price' => static::ACTION_UPDATE_PRICE,
                'update_contact' => static::ACTION_UPDATE_CONTACT,
                'transfer' => static::ACTION_TRANSFER,
            ],
            'links' => [
                'sync_domain' => static::makeUrl(static::ACTION_SYNC),
                'domain_view' => static::makeUrl(static::VIEW_DOMAIN),
                'prev_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page - 1)], $filters)),
                'next_page' => static::makeUrl(static::VIEW_INDEX, array_merge(['page' => ($page + 1)], $filters))
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalDomains);
        $this->setActualView(static::VIEW_INDEX);

        return $this->view('index', $params);
    }

    /**
     * Retrieves template for transfer view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Transfer()
    {
        $page = $this->getRequest()->getParam('page', 1);

        // GET DOMAINS BY PAGINATION

        $whmcsService = $this->getApp()->getService('whmcs');

        $totalDomains = $whmcsService->getDomainsCount(['not_registrars' => 'dondominio']);
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');

        $offset = (($page - 1) * $limit);

        $domains = $whmcsService->getDomains(['not_registrars' => 'dondominio'], $offset, $limit);

        // PARAMS TO TEMPLATE

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'domains' => $domains,
            'actions' => [
                'view_transfer' => static::VIEW_TRANSFER,
                'transfer_domains' => static::ACTION_TRANSFER
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_TRANSFER, ['page' => ($page - 1)]),
                'next_page' => static::makeUrl(static::VIEW_TRANSFER, ['page' => ($page + 1)])
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalDomains);
        $this->setActualView(static::VIEW_TRANSFER);

        return $this->view('transfer', $params);
    }

    /**
     * Retrieves template for import view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Import()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $word = $this->getRequest()->getParam('domain', '');
        $tld = $this->getRequest()->getParam('tld', '');

        // GET DOMAINS BY PAGINATION

        $whmcsService = $this->getApp()->getService('whmcs');

        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');

        $domains = [];
        $totalRecords = 0;

        try {
            $domainsInfo = $this->getApp()->getService('api')->getDomainList($page, $limit, $word, $tld);

            $domains = $domainsInfo->get("domains");
            $totalRecords = $domainsInfo->get("queryInfo")['total'];

            array_walk($domains, function (&$domain) use ($whmcsService) {
                $domain['domain_found'] = $whmcsService->getDomain(['domain' => $domain['name']]);
            });
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $clients = $whmcsService->getClients();
        $filters = [
            'domain' => $word,
            'tld' => $tld,
        ];

        // PARAMS TO TEMPLATE

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'domains' => $domains,
            'customers' => $clients,
            'actions' => [
                'view_import' => static::VIEW_IMPORT,
                'import_domains' => static::ACTION_IMPORT,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_IMPORT, array_merge(['page' => ($page - 1)], $filters)),
                'next_page' => static::makeUrl(static::VIEW_IMPORT, array_merge(['page' => ($page + 1)], $filters))
            ],
            'filters' => $filters,
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_IMPORT);

        return $this->view('import', $params);
    }

    /**
     * Retrieves template for deleted domains history view
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Deleted()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $whmcsService = $this->getApp()->getService('whmcs');
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');

        $domains = [];
        $totalRecords = 0;

        try {
            $response = $this->getApp()->getService('api')->getListDeleted($page, $limit);

            $domains = $response->get('domains');
            $totalRecords = $response->get("queryInfo")['total'];
        } catch (\Throwable $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'domains' => $domains,
            'actions' => [
                'view_deleted' => static::VIEW_DELETED,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_DELETED, ['page' => ($page - 1)]),
                'next_page' => static::makeUrl(static::VIEW_DELETED, ['page' => ($page + 1)])
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_DELETED);

        return $this->view('deleted', $params);
    }

    /**
     * Get Domain info in JSON
     *
     * @return void
     */
    public function view_getInfo()
    {
        $response = $this->getResponse();
        $domain = $this->getRequest()->getParam('domain');
        $app = $this->getApp();

        $verificationTrans = [
            'verified' => 'contact_ver_verified',
            'notapplicable' => 'contact_ver_inprocess',
            'inprocess' => 'contact_ver_notapplicable',
            'failed' => 'contact_ver_failed',
        ];

        try {
            $info = $app->getService('api')->getDomainInfo($domain);
            $infoDNS = $app->getService('api')->getDomainInfo($domain, 'nameservers');
            $verification = $info->get('ownerverification');

            if (isset($verificationTrans[$verification])) {
                $verification = $app->getLang('contact_ver_verified');
            }

            $params = [
                'name' => $info->get('name'),
                'tld' => $info->get('tld'),
                'status' => $info->get('status'),
                'tsExpire' => $info->get('tsExpir'),
                'tsCreate' => $info->get('tsCreate'),
                'ownerverification' => $verification,
                'nameservers' => $infoDNS->get('nameservers'),
            ];
        } catch (Exception $e) {
            $params['error'] = $e->getMessage();
        }

        $response->setContentType(Response::CONTENT_JSON);
        $response->send(json_encode($params), true);
    }

    /**
     * Retrieves template for Domain
     *
     * @return void
     */
    public function view_Domain()
    {
        $id = $this->getRequest()->getParam('domain_id');
        $domain = $this->getApp()->getService('whmcs')->getDomainById($id);

        if (is_null($domain)) {
            $this->getResponse()->addError($this->getApp()->getLang('domain_not_found'));
        }

        $params = [
            'domain' => $domain,
            'module_name' => $this->getApp()->getName(),
            'expire_date' => is_object($domain) ? $domain->expirydate->format('Y-m-d') : '',
            'links' => [
                'get_info' => $this->makeURL(static::VIEW_GETINFO, ['domain' => $domain->domain]),
                'history' => $this->makeURL(static::VIEW_HISTORY, ['domain_id' => $domain->id]),
                'sync' => $this->makeURL(static::ACTION_SYNC, ['domain_id' => $domain->id])
            ],
        ];

        return $this->view('domain', $params);
    }

    /**
     *  Retrieves template for Domain history
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_History()
    {
        $domainId = $this->getRequest()->getParam('domain_id');
        $page = $this->getRequest()->getParam('page', 1);
        $whmcsService = $this->getApp()->getService('whmcs');
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');
        $domain = $whmcsService->getDomainById($domainId);

        try {
            if (is_null($domain)) {
                throw new Exception('domain_not_found');
            }

            $response = $this->getApp()->getService('api')->getDomainHistory($domain->domain, $page, $limit);

            $history = $response->get('history');
            $totalRecords = $response->get("queryInfo")['total'];
        } catch (\Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $params = [
            'action' => static::VIEW_HISTORY,
            'domain' => $domain,
            'expire_date' => is_object($domain) ? $domain->expirydate->format('Y-m-d') : '',
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'history' => $history,
            'actions' => [
                'view_deleted' => static::VIEW_DELETED,
            ],
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_HISTORY, ['page' => ($page - 1), 'domain_id' => $domainId]),
                'next_page' => static::makeUrl(static::VIEW_HISTORY, ['page' => ($page + 1), 'domain_id' => $domainId]),
                'get_info' => $this->makeURL(static::VIEW_GETINFO, ['domain' => $domain->domain]),
                'history' => $this->makeURL(static::VIEW_HISTORY, ['domain_id' => $domain->id]),
                'sync' => $this->makeURL(static::ACTION_SYNC, ['domain_id' => $domain->id])
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);

        return $this->view('domain', $params);
    }

    /**
     * View for contacts list
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Contacts()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $name = $this->getRequest()->getParam('name');
        $email = $this->getRequest()->getParam('email');
        $verification = $this->getRequest()->getParam('verification');
        $daaccepted = $this->getRequest()->getParam('daaccepted');

        $app = App::getInstance();
        $apiService = $app->getService('api');
        $whmcsService = $app->getService('whmcs');
        $limit = $whmcsService->getConfiguration('NumRecordstoDisplay');
        $totalRecords = 0;
        $contactsData = null;

        try {
            $contacts = $apiService->getContactList($page, $limit, $name, $email, $verification, $daaccepted);
            $totalRecords = $contacts->get('queryInfo')['total'];
            $contactsData =  $contacts->getResponseData()['contacts'];
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        $verificationOptions = [
            'verified' => $app->getLang('contact_ver_verified'),
            'inprocess' => $app->getLang('contact_ver_inprocess'),
            'failed' => $app->getLang('contact_ver_failed'),
            'notapplicable' => $app->getLang('contact_ver_notapplicable'),
        ];

        $daacceptedOptions = [
            0 => $app->getLang('contact_da_no_accepted'),
            1 => $app->getLang('contact_da_accepted'),
        ];

        $filters = [
            'name' => $name,
            'email' => $email,
            'verification' => $verification,
            'daaccepted' => $daaccepted,
        ];

        $params = [
            'module_name' => $this->getApp()->getName(),
            '__c__' => static::CONTROLLER_NAME,
            'contacts' => $contactsData,
            'actions' => [
                'view_contacts' => static::VIEW_CONTACTS,
            ],
            'options' => [
                'verification' => $verificationOptions,
                'daaccepted' => $daacceptedOptions,
            ],
            'filters' => $filters,
            'links' => [
                'prev_page' => static::makeUrl(static::VIEW_CONTACTS, array_merge(['page' => ($page - 1)], $filters)),
                'next_page' => static::makeUrl(static::VIEW_CONTACTS, array_merge(['page' => ($page + 1)], $filters)),
                'contact' => static::makeUrl(static::VIEW_CONTACT),
            ],
        ];

        $this->setPagination($params, $limit, $page, $totalRecords);
        $this->setActualView(static::VIEW_CONTACTS);

        return $this->view('contacts', $params);
    }

    /**
     * View for contact
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view_Contact()
    {
        $contactID = $this->getRequest()->getParam('contact_id');
        $api = $this->getApp()->getService('api');
        $contactData = null;

        try {
            $contact = $api->getContactInfo($contactID);
            $contactData = $contact->getResponseData();
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }


        $params = [
            'contact' => $contactData,
            'links' => [
                'contact_resend' => static::makeURL(static::action_RESENDVERIFICATIONMAIL, ['contact_id' => $contactID])
            ]
        ];

        return $this->view('contact', $params);
    }

    /**
     * Resend the contact details verification email
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_ResendVerificationMail()
    {
        $contactID = $this->getRequest()->getParam('contact_id');
        $api = $this->getApp()->getService('api');

        try {
            $api->getContactResendVerificationMail($contactID);

            $this->getResponse()->addSuccess($this->getApp()->getLang('contact_success_resend'));
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Contact();
    }

    /**
     * Syncs many domains (massively)
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Sync()
    {
        try {
            $domainId = $this->getRequest()->getParam('domain_id');
            $ids = $this->getRequest()->getArrayParam('domain_checkbox');

            if (!empty($domainId)) {
                $ids[] = $domainId;
            }

            $whmcsService = $this->getApp()->getService('whmcs');

            if (empty($ids)) {
                throw new Exception('domains_no_domains_selected');
            }

            foreach ($ids as $id) {
                $domain = $whmcsService->getDomainById($id);

                try {
                    if (is_null($domain)) {
                        throw new Exception('domain_not_found');
                    }

                    $whmcsService->syncDomain($domain);

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $domain->domain, $this->getApp()->getLang('domain_synced_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $id), $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return empty($domainId) ? $this->view_Index() : $this->view_Domain();
    }

    /**
     * Switch registrar to 'dondominio' for many domains (massively)
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_SwitchRegistrar()
    {
        try {
            $ids = $this->getRequest()->getArrayParam('domain_checkbox');

            $whmcsService = $this->getApp()->getService('whmcs');

            if (empty($ids)) {
                throw new Exception('domains_no_domains_selected');
            }

            foreach ($ids as $id) {
                $domain = $whmcsService->getDomainById($id);

                try {
                    if (is_null($domain)) {
                        throw new Exception('domain_not_found');
                    }

                    $whmcsService->switchRegistrar($domain, 'dondominio');

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $domain->domain, $this->getApp()->getLang('domain_updated_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $id), $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Updates renewal prices for many domains (massively)
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_UpdatePrice()
    {
        try {
            $ids = $this->getRequest()->getArrayParam('domain_checkbox');

            $whmcsService = $this->getApp()->getService('whmcs');

            if (empty($ids)) {
                throw new Exception('domains_no_domains_selected');
            }

            foreach ($ids as $id) {
                $domain = $whmcsService->getDomainById($id);

                try {
                    if (is_null($domain)) {
                        throw new Exception('domain_not_found');
                    }

                    $whmcsService->updateRecurringPrice($domain);

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $domain->domain, $this->getApp()->getLang('domain_updated_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $id), $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Updates contact for many domains (via API)
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_updateContact()
    {
        try {
            $ids = $this->getRequest()->getArrayParam('domain_checkbox');
            $type = $this->getRequest()->getParam('form_action');
            $ddid = $this->getRequest()->getParam('ddid');

            if (empty($ids)) {
                throw new Exception('domains_no_domains_selected');
            }

            if (strlen($ddid) == 0) {
                throw new Exception(
                    $this->getApp()->getLang('domains_error_dondominio_id') .
                        ". <a href='https://dev.mrdomain.com/whmcs/docs/addon/#contacts' target='_api'>" .
                        $this->getApp()->getLang('link_more_info') . "</a>"
                );
            }

            foreach ($ids as $id) {
                $domain = $this->getApp()->getService('whmcs')->getDomainById($id);

                try {
                    if (is_null($domain)) {
                        throw new Exception('domain_not_found');
                    }

                    $this->getApp()->getService('api')->updateContact($domain->domain, $type, $ddid);

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $domain->domain, $this->getApp()->getLang('domain_updated_succesfully'))
                    );
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $id), $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Index();
    }

    /**
     * Transfer domains from other registrars to dondominio
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Transfer()
    {
        try {
            $redirectIndex = $this->getRequest()->getParam('redirect_index');
            $ids = $this->getRequest()->getArrayParam('domain_checkbox');
            $authCodes = $this->getRequest()->getArrayParam('authcode');

            $map = [];
            foreach ($ids as $key => $id) {
                $map[$id] = $authCodes[$key];
            }

            if (empty($map)) {
                throw new Exception('domains_no_domains_selected');
            }

            foreach ($map as $id => $authCode) {
                $domain = $this->getApp()->getService('whmcs')->getDomainById($id);

                try {
                    if (is_null($domain)) {
                        throw new Exception('domain_not_found');
                    }

                    $this->getApp()->getService('whmcs')->transferDomain($domain, $authCode);

                    $this->getResponse()->addSuccess(
                        sprintf(' - %s %s', $domain->domain, $this->getApp()->getLang('transfer_success'))
                    );
                } catch (Exception $e) {
                    if ($e->getCode() == 2005) {
                        $this->getResponse()->addInfo(
                            sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $id), $this->getApp()->getLang($e->getMessage()))
                        );
                    } else {
                        $this->getResponse()->addError(
                            sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $id), $this->getApp()->getLang($e->getMessage()))
                        );
                    }
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return empty($redirectIndex) ? $this->view_Transfer() : $this->view_Index();
    }

    /**
     * Import domains from Dondominio account to WHMCS
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function action_Import()
    {
        try {
            $domains = $this->getRequest()->getArrayParam('domain_checkbox');
            $customerid = $this->getRequest()->getParam('customer');

            if (empty($domains)) {
                throw new Exception('domains_no_domains_selected');
            }

            $order = $this->getApp()->getService('whmcs')->insertOrderWithUserId($customerid);

            if (!is_object($order)) {
                throw new Exception('create_order_error');
            }

            foreach ($domains as $domainName) {
                $domain = $this->getApp()->getService('whmcs')->getDomain(['domain' => $domainName]);

                try {
                    // If found, we must not import it 
                    if (!is_null($domain)) {
                        $this->getResponse()->addInfo(' - ' . $domain->domain . ' ' . $this->getApp()->getLang('was_already_imported'));
                        continue;
                    }

                    $domain = $this->getApp()->getService('whmcs')->importDomain($domainName, $customerid, $order->id);

                    $this->getResponse()->addSuccess(sprintf(' - %s %s', $domain->domain, $this->getApp()->getLang('imported_successfully')));
                } catch (Exception $e) {
                    $this->getResponse()->addError(
                        sprintf(' - %s %s', (!is_null($domain) ? $domain->domain : $domainName), $this->getApp()->getLang($e->getMessage()))
                    );
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->addError($this->getApp()->getLang($e->getMessage()));
        }

        return $this->view_Import();
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

        $params['title'] = $app->getLang('content_title_domains');
        $params['nav'] = [
            [
                'title' => $app->getLang('domains_title'),
                'link' => static::makeURL(static::VIEW_INDEX),
                'selected' => $this->checkActualView(static::VIEW_INDEX)
            ],
            [
                'title' => $app->getLang('transfer_title'),
                'link' => static::makeURL(static::VIEW_TRANSFER),
                'selected' => $this->checkActualView(static::VIEW_TRANSFER)
            ],
            [
                'title' => $app->getLang('import_title'),
                'link' => static::makeURL(static::VIEW_IMPORT),
                'selected' => $this->checkActualView(static::VIEW_IMPORT)
            ],
            [
                'title' => $app->getLang('deleted_domains_title'),
                'link' => static::makeURL(static::VIEW_DELETED),
                'selected' => $this->checkActualView(static::VIEW_DELETED)
            ],
            [
                'title' => $app->getLang('contacts_title'),
                'link' => static::makeURL(static::VIEW_CONTACTS),
                'selected' => $this->checkActualView(static::VIEW_CONTACTS)
            ],
        ];

        return parent::view($view, $params);
    }
}
