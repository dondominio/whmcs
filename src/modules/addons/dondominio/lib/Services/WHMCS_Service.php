<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Domain\Domain;
use WHMCS\Database\Capsule;
use Carbon\Carbon;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\WHMCSService_Interface;
use WHMCS\Module\Addon\Dondominio\Models\Pricing_Model;
use Exception;

class WHMCS_Service extends AbstractService implements WHMCSService_Interface
{
    /**
     * Makes a local API Call
     *
     * @see https://developers.whmcs.com/api/internal-api/
     *
     * @param string $command Command to execute
     * @param array $values Array as parameters
     * @param string $adminUser User
     *
     * @throws Exception if success went wrong
     */
    public function doLocalAPICall($command, array $values, $adminUser = null)
    {
        if (!function_exists('localAPI')) {
            throw new Exception('Function localAPI not found. Are you sure you using WHMCS?');
        }

        $response = localAPI($command, $values, $adminUser);

        if (!array_key_exists('result', $response)) {
            throw new Exception('[LOCAL API ERROR] Function localAPI invalid response.');
        }

        if ($response['result'] == 'error') {
            throw new Exception(
                '[LOCAL API ERROR] ' .
                (array_key_exists('message', $response) ? $response['message'] : 'Something went wrong with localAPI')
            );
        }

        return $response;
    }

    /**
     * Get currency by code
     * 
     * @param string $currency Code of currency
     * @return null|array
     */
    public function getCurrency($currency)
    {
        $response = $this->doLocalAPICall('GetCurrencies', []);

        if (!array_key_exists('currencies', $response)) {
            throw new Exception('[LOCAL API ERROR] Currencies index not found.');
        }

        if (!array_key_exists('currency', $response['currencies'])) {
            throw new Exception('[LOCAL API ERROR] Currency index not found.');
        }

        foreach ($response['currencies']['currency'] as $c) {
            if ($currency == $c['code']) {
                return $c;
            }
        }

        return null;
    }

    /**
     * Return value from one row in `tblconfiguration`
     *
     * @param string $setting Setting to search 
     * @return mixed value of requested setting
     */
    public function getConfiguration($setting)
    {
        $response = $this->doLocalAPICall('GetConfigurationValue', ['setting' => $setting]);

        return array_key_exists('value', $response) ? $response['value'] : null;
    }

    /**
     * Retreives domains cursor from 'tbldomains`
     *
     * @param null|string $domain Domain
     * @param null|string $registrar Registrar
     * @param null|string $status Status
     * @param null|string $tld Tld
     * @param null|int $offset Offset to start query results
     * @param null|int $limit Limit to end query results
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Domain objects containting domains info
     */
    public function getDomains(array $filters = [], $offset = null, $limit = null)
    {
        $queryBuilder = Domain::select();

        if (array_key_exists('domain', $filters) && !empty($filters['domain'])) {
            $queryBuilder->where('domain', 'like', '%' . $filters['domain'] . '%');
        }

        if (array_key_exists('registrar', $filters) && !empty($filters['registrar'])) {
            $queryBuilder->where(['registrar' => $filters['registrar']]);
        }

        if (array_key_exists('not_registrars', $filters) && !empty($filters['not_registrars'])) {
            $queryBuilder->whereNotIn('registrar', [$filters['not_registrars']]);
        }

        if (array_key_exists('status', $filters) && !empty($filters['status'])) {
            $queryBuilder->where(['status' => $filters['status']]);
        }

        if (array_key_exists('tld', $filters) && !empty($filters['tld'])) {
            $tld = $filters['tld'];
            $queryBuilder->whereRaw('SUBSTRING(`domain` FROM -? FOR ?) = ?', [strlen($tld), strlen($tld), $tld]);
        }

        if (!is_null($offset)) {
            $queryBuilder->offset($offset);
        }

        if (!is_null($limit)) {
            $queryBuilder->limit($limit);
        }

        $queryBuilder->orderBy('domain', 'ASC');

        //var_dump($queryBuilder->toSql());

        return $queryBuilder->get();
    }

    /**
     * Retrieves count for `tbldomains`
     *
     * @param null|string $domain Domain
     * @param null|string $registrar Registrar
     * @param null|string $status Status
     * @param null|string $tld Tld
     *
     * @return int
     */
    public function getDomainsCount(array $filters)
    {
        $queryBuilder = Domain::select();

        if (array_key_exists('domain', $filters) && !empty($filters['domain'])) {
            $queryBuilder->where('domain', 'like', '%' . $filters['domain'] . '%');
        }

        if (array_key_exists('registrar', $filters) && !empty($filters['registrar'])) {
            $queryBuilder->where(['registrar' => $filters['registrar']]);
        }

        if (array_key_exists('not_registrars', $filters) && !empty($filters['not_registrars'])) {
            $queryBuilder->whereNotIn('registrar', [$filters['not_registrars']]);
        }

        if (array_key_exists('status', $filters) && !empty($filters['status'])) {
            $queryBuilder->where(['status' => $filters['status']]);
        }

        if (array_key_exists('tld', $filters) && !empty($filters['tld'])) {
            $tld = $filters['tld'];
            $queryBuilder->whereRaw('SUBSTRING(`domain` FROM -? FOR ?) = ?', [strlen($tld), strlen($tld), $tld]);
        }

         //var_dump($queryBuilder->toSql());

        return $queryBuilder->count();
    }

    /**
     * Finds domain with extra information (client and custom fields related)
     *
     * @see Illuminate\Database\Connection:selectOne()
     *
     * @param string $id Vat Number
     *
     * @return mixed|null
     */
    public function getDomainExtendedById($id)
    {
        return  Capsule::selectOne('
            SELECT *, (
                SELECT
                    value
                FROM tblcustomfieldsvalues
                WHERE
                    relid = C.id
                    AND fieldid = ( 
                        SELECT
                            id
                        FROM tblcustomfields
                        WHERE
                            fieldname = "Vat Number"
                    )
                ) AS vatnumber
            FROM tbldomains D
            LEFT JOIN tblclients C ON C.id = D.userid
            WHERE D.id = ?',
            [$id]
        );
    }

    /**
     * Retrieves a list of Domain Pricings (Tlds)
     *
     * @return array Array of Domain Pricing like ['id' => 'extension']
     */
    public function getDomainPricingsForSelect()
    {
        return Capsule::table('tbldomainpricing')->orderBy('extension', 'ASC')->pluck('extension', 'extension');
    }

    /**
     * Retrieves price for a TLD
     *
     * @param int $tld id from `tbldomainpricing` table
     * @param int $currency id from `tblcurrencies` table
     * @return string|null price from `tblpricing`
     */
    public function getPricing($type, $tld, $currency, $field)
    {
        return Capsule::table('tblpricing')->where(['type' => $type, 'relid' => $tld, 'currency' => $currency])->value($field);
    }

    /**
     * Inserts pricing in tblpricing table
     *
     * @param string $type Type (domainregister, domaintransfer, domainrenew...)
     * @param int $tld_id Domain pricing ID (tbldomainpricing table)
     * @param int $currency_id Currency ID (tblcurrencies table)
     *
     * @return bool
     */
    public function insertPricing($type, $tld_id, $currency_id)
    {
        return Capsule::table('tblpricing')->insert([
            'type' => $type,
            'relid' => $tld_id,
            'currency' => $currency_id,
            'msetupfee' => 0,
            'qsetupfee' => 0,
            'ssetupfee' => 0,
            'asetupfee' => 0,
            'bsetupfee' => 0,
            'tsetupfee' => 0,
            'monthly' => 0,
            'quarterly' => 0,
            'semiannually' => 0,
            'annually' => 0,
            'biennially' => 0,
            'triennially' => 0
        ]);
    }

    /**
     * Retrieves a list with distincts registrars (from `tbldomain`)
     *
     * @return Illuminate\Support\Collection
     */
    public function getDisctintRegistrars()
    {
        return Domain::distinct('registrar')->orderBy('registrar', 'ASC')->pluck('registrar', 'registrar');
    }

    /**
     * Retrieves a domain
     *
     * @param int $id ID
     *
     * @return null|Domain
     */
    public function getDomainById($id)
    {
        return Domain::find($id);
    }

    /**
     * Retrieves a domain
     *
     * @param aray $where Where conditions in array form
     *
     * @return null|Domain
     */
    public function getDomain(array $where)
    {
        return Domain::where($where)->first();
    }

    /**
     * Downloads information from API and updates domains
     *
     * @param \WHMCS\Domain\Domain
     *
     * @return void
     */
    public function syncDomain(Domain $domain)
    {
        $info = $this->getApp()->getService('api')->getDomainInfo($domain->domain);

        $status = 'Pending';

        $mapStatus = [
            'Active' => [
                'active',
                'renewed'
            ],
            'Expired' => [
                'expired-renewgrace',
                'expired-redemption',
                'expired-pendingdelete'
            ]
        ];

        foreach ($mapStatus as $key => $statusArr) {
            if (in_array($info->get('status'), $statusArr)) {
                $status = $key;
                break;
            }
        }

        $domain->expirydate = $info->get("tsExpir");
        $domain->status = $status;

        $domain->save();
    }

    /**
     * Change registrar from table `tbldomains`
     *
     * @param \WHMCS\Domain\Domain
     * @param string $registrar New registrar to update
     * @param string $status New status
     *
     * @return void
     */
    public function switchRegistrar(Domain $domain, $registrar, $status = null)
    {
        $domain->registrar = $registrar;

        if (!is_null($status)) {
            $domain->status = $status;
        }

        $domain->save();
    }

    /**
     * Update Domain recurring price from TLDs prices table
     *
     * @param \WHCSM\Domain $domain
     *
     * @throws \Exception If TLD not valid, currency EUR not found, TLD Price not found
     *
     * @return void
     */
    public function updateRecurringPrice(Domain $domain)
    {
        if ($domain->registrar != 'dondominio') {
            throw new Exception('registrar_not_dondominio');
        }

        $dot = strpos($domain->domain, '.');

        if (!$dot) {
            throw new Exception('domains_tld_not_valid');
        }

        $extension = substr($domain->domain, $dot);

        // Get TLD
        $tld = Capsule::table('tbldomainpricing')->where(['extension' => $extension, 'autoreg' => 'dondominio'])->first();

        if (is_null($tld)) {
            throw new Exception('domains_tld_not_valid');
        }

        $currency = $this->getCurrency('EUR');

        if (is_null($currency)) {
            throw new Exception('domains_eur_not_found');
        }

        $pricing = $this->getPricing('domainrenew', $tld->id, $currency['id'], 'msetupfee');

        if (is_null($pricing)) {
            throw new Exception('domains_tld_price_not_found');
        }

        $domain->recurringamount = $pricing;
        $domain->save();
    }

    /**
     * Send domain transfer petition to API
     *
     * @param Domain $domain Domain to transfer to dondominio
     * @param string $authCode Authcode if necessary
     *
     * @throws \Exception If domain not found, If Transfer error
     *
     * @return void
     */
    public function transferDomain(Domain $domain, $authCode = '')
    {
        try {
            $extDomain = $this->getDomainExtendedById($domain->id);

            if (is_null($extDomain)) {
                throw new Exception('domain_not_found');
            }

            /*
            * Requesting TLD information
            */
            $domainComponents = explode(".", $extDomain->domain);

            //var_dump($domainComponents);

            if (!is_array($domainComponents)) {
                throw new Exception('transfer_invalid_domain_name');
            }

            $tld = $domainComponents[count($domainComponents) - 1];

            $tldInfo = $this->getApp()->getService('pricing')->findPricingByTld($tld);

            if (is_null($tldInfo)) {
                throw new Exception('transfer_tld_not_found');
            }

            if ($tldInfo->authcode_required && empty($authCode)) {
                throw new Exception('transfer_authcode_required');
            }

            $params = [
                'clientid' => $extDomain->userid,
                'stats' => true,
                'responsetype' => 'json'
            ];

            $clientDetails = $this->doLocalAPICall("GetClientsDetails", $params);

            if (!is_array($clientDetails)) {
                throw new Exception('transfer_client_not_found');
            }

            if (empty($extDomain->vatnumber)) {
                throw new Exception('transfer_vatnumber_empty');
            }

            $this->getApp()->getService('api')->transferDomain($extDomain, $authCode, $clientDetails);
            $this->switchRegistrar($domain, 'dondominio', 'Pending Transfer');
        } catch (Exception $e) {
            if ($e->getCode() == 2005) {
                $this->switchRegistrar($domain, 'dondominio', 'Active');
            }

            throw $e;
        }
    }

    /**
     * Returns clients from DDBB (tblclients table)
     *
     * @return \Illuminate\Support\Collection Collection of clients
     */
    public function getClients()
    {
        return Capsule::table('tblclients')->get();
    }

    /**
     * Insert order related to userid (tblorders table)
     *
     * @throws \Exception if error while creating order
     *
     * @return null|\stdClass
     */
    public function insertOrderWithUserId($userid)
    {
        if (!is_numeric($userid) || $userid <= 0) {
            throw new Exception('no_customer_selected');
        }

        $insert = Capsule::table('tblorders')->insert([
            'ordernum' => 1,
            'userid' => $userid,
            'contactid' => 0,
            'date' => Carbon::now()->toDateTimeString(),
            'amount' => '0.00',
            'invoiceid' => 0,
            'status' => 'Active',
            'notes' => 'Created by DonDominio WHMCS Addon'
        ]);

        if (!$insert) {
            throw new Exception('Error while creating order.');
        }

        return Capsule::table('tblorders')->latest('id')->first();
    }

    /**
     * Retrieves information from API and saves into the system
     *
     * @param string $apiDomainId Domain ID (from dondominio)
     * @param string $customerId Customer ID related
     * @param string|int $orderId Order ID related
     *
     * @return WHMCS\Domain\Domain saved Domain
     */
    public function importDomain($apiDomainId, $customerId, $orderId)
    {
        $info = $this->getApp()->getService('api')->getDomainInfo($apiDomainId);

        $domain = new Domain();
        $domain->userid = $customerId;
        $domain->orderid = $orderId;
        $domain->type = 'Register';
        $domain->registrationdate = $info->get("tsCreate");
        $domain->domain = $info->get("name");
        $domain->firstpaymentamount = '0.00';
        $domain->recurringamount = '0.00';
        $domain->registrar = 'dondominio';
        $domain->registrationperiod = 1;
        $domain->expirydate = $info->get("tsExpir");
        $domain->subscriptionid = '';
        $domain->promoid = 0;
        $domain->status = 'Active';
        $domain->nextduedate = $info->get("tsExpir");
        $domain->nextinvoicedate = $info->get("tsExpir");
        $domain->additionalnotes = $this->getApp()->getLang('created_by_whmcs_dondominio_addon');
        $domain->synced = 0;

        $domain->save();

        return $domain;
    }

    /**
     * Retrieves number of domain pricings in DDBB (tbldomainpricing table)
     *
     * @return int Number of domain pricing
     */
    public function getDomainPricingsCount()
    {
        return Capsule::table('tbldomainpricing')->count();
    }

    /**
     * Retrieves domain pricings from DDBB (tbldomainpricing table)
     *
     * @param array $filters to filter results
     * @param int|string $offset index where query starts
     * @param int|string $limit limit where query ends
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDomainPricings(array $filters = [], $offset = null, $limit = null, array $order = ['extension' => 'ASC'])
    {
        $queryBuilder = Capsule::table('tbldomainpricing');

        if (array_key_exists('autoreg', $filters) && !empty($filters['autoreg'])) {
            $queryBuilder->where(['autoreg' => $filters['autoreg']]);
        }

        if (!is_null($offset)) {
            $queryBuilder->offset($offset);
        }

        if (!is_null($limit)) {
            $queryBuilder->limit($limit);
        }

        foreach ($order as $field => $direction) {
            $queryBuilder->orderBy($field, $direction);
        }

        return $queryBuilder->get();
    }

    /**
     * Retrieves first domain pricing matching filters
     *
     * @param array $filters where conditions
     *
     * @return null|\stdClass
     */
    public function getDomainPricing(array $filters = [])
    {
        return Capsule::table('tbldomainpricing')->where($filters)->first();
    }

    /**
     * Selects all dondominio TLDs and inserts non-euro-currencies based on euro currency
     *
     * @return void
     */
    public function insertPricingsForOtherCurrencies()
    {
        $domainPricings = $this->getDomainPricings(['autoreg' => 'dondominio'], null, null, ['id' => 'ASC']);
        $currencies = Capsule::table('tblcurrencies')->whereNotIn('code', ['EUR'])->get();

        foreach ($domainPricings as $domainPricing) {
            foreach ($currencies as $currency) {
                $create = $this->getPricing('domainregister', $domainPricing->id, $currency->id, 'id');
                $transfer = $this->getPricing('domaintransfer', $domainPricing->id, $currency->id, 'id');
                $renew = $this->getPricing('domainrenew', $domainPricing->id, $currency->id, 'id');

                if (is_null($create)) {
                    $this->insertPricing('domainregister', $domainPricing->id, $currency->id);
                }

                if (is_null($transfer)) {
                    $this->insertPricing('domaintransfer', $domainPricing->id, $currency->id);
                }

                if (is_null($renew)) {
                    $this->insertPricing('domainrenew', $domainPricing->id, $currency->id);
                }
            }
        }
    }

    /**
     * Given a pricing from mod_dondominio_pricing, saves it into whmcs pricings table (tblpricing table)
     *
     * @param Pricing_Model $tld an object from mod_dondominio_pricing
     *
     * @return void
     */
    public function savePricingsForEur(Pricing_Model $tld)
    {
        $currency = $this->getCurrency('EUR');

        if (is_null($currency)) {
            throw new Exception('domains_eur_not_found');
        }

        $domainPricing = $this->getDomainPricing(['extension' => $tld->tld, 'autoreg' => 'dondominio']);

        if (is_null($domainPricing)) {
            throw new Exception('domains_tld_not_valid');
        }

        $register_price = $tld->register_price;
        $transfer_price = $tld->transfer_price;
        $renew_price = $tld->renew_price;

        $register = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $whmcsVersion = $this->getConfiguration('version');

        if (version_compare($whmcsVersion, '6.0.0', '>=')) {
            $register = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1];
        }

        $transfer = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1];
        $renew = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1];

        $settingsService = $this->getApp()->getService('settings');

        $register_increment = $settingsService->getSetting("register_increase");
        $transfer_increment = $settingsService->getSetting("transfer_increase");
        $renew_increment = $settingsService->getSetting("renew_increase");

        $register_increment_type = $settingsService->getSetting("register_increase_type");
        $transfer_increment_type = $settingsService->getSetting("transfer_increase_type");
        $renew_increment_type = $settingsService->getSetting("renew_increase_type");

        /*
        * Tld custom settings
        */
        $custom_settings = $this->getApp()->getService('tld_settings')->getTldSettingsByTld($tld->tld);

        if (!is_null($custom_settings) && $custom_settings->enabled == 1) {
            $register_increment = $custom_settings->register_increase;
            $transfer_increment = $custom_settings->transfer_increase;
            $renew_increment = $custom_settings->renew_increase;

            $register_increment_type = $custom_settings->register_increase_type;
            $transfer_increment_type = $custom_settings->transfer_increase_type;
            $renew_increment_type = $custom_settings->renew_increase_type;
        }

        if ($register_increment > 0) {
            switch ($register_increment_type) {
                case 'fixed':
                    $register_price += $register_increment;
                break;
                case 'no_increase':
                    $register_price = $register_increment;
                break;
                default:
                    $register_price = $register_price * (($register_increment / 100) + 1);
                break;
            }
        }

        if ($transfer_increment > 0) {
            switch ($transfer_increment_type) {
                case 'fixed':
                    $transfer_price += $transfer_increment;
                break;
                case 'no_increase':
                    $transfer_price = $transfer_increment;
                break;
                default:
                    $transfer_price = $transfer_price * (($transfer_increment / 100) + 1);
                break;
            }
        }

        if ($renew_increment > 0) {
            switch ($renew_increment_type) {
                case 'fixed':
                    $renew_price += $renew_increment;
                break;
                case 'no_increase':
                    $renew_price = $renew_increment;
                break;
                default:
                    $renew_price = $renew_price * (($renew_increment / 100) + 1);
                break;
            }
        }

        //Register
        $register_terms = explode(',', $tld->register_range);

        foreach ($register_terms as $term) {
            if (strpos($term, '-')) {
                $range = explode('-', $term);

                for ($i = $range[0]; $i <= $range[1]; $i++) {
                    $register[$i - 1] = $register_price * $i;
                }
            } else {
                $register[$term - 1] = $register_price * $term;
            }
        }

        //Transfer
        $transfer_terms = explode(',', $tld->transfer_range);

        foreach ($transfer_terms as $term) {
            if (strpos($term, '-')) {
                $range = explode('-', $term);

                for ($i = $range[0]; $i <= $range[1]; $i++) {
                    $transfer[$i - 1] = $transfer_price * $i;
                }
            }else{
                $transfer[$term - 1] = $transfer_price * $term;
            }
        }

        //Renew
        $renew_terms = explode(',', $tld->renew_range);

        foreach ($renew_terms as $term) {
            if (strpos($term, '-')) {
                $range = explode('-', $term);
                
                for ($i = $range[0]; $i <= $range[1]; $i++) {
                    $renew[$i - 1] = $renew_price * $i;
                }
            }else{
                $renew[$term - 1] = $renew_price * $term;
            }
        }

        // UPDATE tblpricing SET ... WHERE type = 'domainregister' AND 'currency' = $curency AND relid = (SELECT id FROM tbldomainpricing WHERE extension = '$extension' AND autoreg = 'dondominio')

        Capsule::table('tblpricing')
            ->updateOrInsert([
                'type' => 'domainregister',
                'currency' => $currency['id'],
                'relid' => $domainPricing->id
            ], [
                'msetupfee' => $register[0],
                'qsetupfee' => $register[1],
                'ssetupfee' => $register[2],
                'asetupfee' => $register[3],
                'bsetupfee' => $register[4],
                'monthly' => $register[5],
                'quarterly' => $register[6],
                'semiannually' => $register[7],
                'annually' => $register[8],
                'biennially' => $register[9]
            ]);

        // UPDATE tblpricing SET ... WHERE type = 'domaintransfer' AND 'currency' = $curency AND relid = (SELECT id FROM tbldomainpricing WHERE extension = '$extension' AND autoreg = 'dondominio')

        Capsule::table('tblpricing')
            ->updateOrInsert([
                'type' => 'domaintransfer',
                'currency' => $currency['id'],
                'relid' => $domainPricing->id
            ], [
                'msetupfee' => $transfer[0],
                'qsetupfee' => $transfer[1],
                'ssetupfee' => $transfer[2],
                'asetupfee' => $transfer[3],
                'bsetupfee' => $transfer[4],
                'monthly' => $transfer[5],
                'quarterly' => $transfer[6],
                'semiannually' => $transfer[7],
                'annually' => $transfer[8],
                'biennially' => $transfer[9]
            ]);

        // UPDATE tblpricing SET ... WHERE type = 'domainrenew' AND 'currency' = $curency AND relid = (SELECT id FROM tbldomainpricing WHERE extension = '$extension' AND autoreg = 'dondominio')
        
        Capsule::table('tblpricing')
            ->updateOrInsert([
                'type' => 'domainrenew',
                'currency' => $currency['id'],
                'relid' => $domainPricing->id
            ], [
                'msetupfee' => $renew[0],
                'qsetupfee' => $renew[1],
                'ssetupfee' => $renew[2],
                'asetupfee' => $renew[3],
                'bsetupfee' => $renew[4],
                'monthly' => $renew[5],
                'quarterly' => $renew[6],
                'semiannually' => $renew[7],
                'annually' => $renew[8],
                'biennially' => $renew[9]
            ]);
    }

    /**
     * Updates pricings for non-euro-currencies based on euro pricings (tblpricing table)
     *
     * @return int Number of affected rows (How many prices have been updated)
     */
    public function updatePricingsForOtherCurrencies()
    {
        $affectedRows = 0;

        $s_base = "
            SELECT
                *
            FROM tblpricing
            WHERE
                (type = 'domainregister' OR type = 'domaintransfer' OR type = 'domainrenew')
                AND relid IN (SELECT id FROM tbldomainpricing WHERE autoreg = 'dondominio')
                AND currency = (SELECT id FROM tblcurrencies WHERE code = 'EUR')
            ORDER BY relid ASC
        ";

        $base = Capsule::select($s_base);

        if (count($base) == 0) {
            return $affectedRows;
        }

        foreach ($base as $pricing) {
            $query = Capsule::table('tblpricing')
                ->where('relid', $pricing->relid)
                ->where('type', $pricing->type)
                ->whereNotIn('currency', [$pricing->currency]);

            $updates = [
                'msetupfee' => '-1',
                'qsetupfee' => '-1',
                'ssetupfee' => '-1',
                'asetupfee' => '-1',
                'bsetupfee' => '-1',
                'monthly' => '-1',
                'quarterly' => '-1',
                'semiannually' => '-1',
                'annually' => '-1',
                'biennially' => '-1'
            ];

            //msetupfee - 1Y
            if ($pricing->msetupfee >= 0) {
                $updates['msetupfee'] = Capsule::raw("(($pricing->msetupfee / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //qsetupfee - 2Y
            if ($pricing->qsetupfee >= 0) {
                $updates['qsetupfee'] = Capsule::raw("(($pricing->qsetupfee / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //ssetupfee - 3Y
            if ($pricing->ssetupfee >= 0) {
                $updates['ssetupfee'] = Capsule::raw("(($pricing->ssetupfee / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //asetupfee - 4Y
            if ($pricing->asetupfee >= 0) {
                $updates['asetupfee'] = Capsule::raw("(($pricing->asetupfee / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //bsetupfee - 5Y
            if ($pricing->bsetupfee >= 0) {
                $updates['bsetupfee'] = Capsule::raw("(($pricing->bsetupfee / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //monthly - 6Y
            if ($pricing->monthly >= 0) {
                $updates['monthly'] = Capsule::raw("(($pricing->monthly / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //quarterly - 7Y
            if ($pricing->quarterly >= 0) {
                $updates['quarterly'] = Capsule::raw("(($pricing->quarterly / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //semianually - 8Y
            if ($pricing->semiannually >= 0) {
                $updates['semiannually'] = Capsule::raw("(($pricing->semiannually / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //annually - 9Y
            if ($pricing->annually >= 0) {
                $updates['annually'] = Capsule::raw("(($pricing->annually / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            //biennially - 10Y
            if ($pricing->biennially >= 0) {
                $updates['biennially'] = Capsule::raw("(($pricing->biennially / (SELECT `rate` FROM `tblcurrencies` WHERE `code` = 'EUR')) * (SELECT `rate` FROM `tblcurrencies` WHERE `id` = `currency`))");
            }

            $affectedRows += $query->update($updates);
        }

        return $affectedRows;
    }

    /**
     * Updates prices for domains (tbldomains table)
     *
     * @return int Number of rows affected (How many domains have been updated)
     */
    public function updateDomainPrices()
    {
        $affectedRows = 0;

        $domains = Domain::select('id', 'domain')->get();

        foreach ($domains as $domain) {
            $cname_components = explode('.', $domain->domain);
            $last_component = count($cname_components) - 1;
            $tld = $cname_components[$last_component];

            $affectedRows += Capsule::update("
                UPDATE tbldomains SET recurringamount = (
                    SELECT msetupfee FROM tblpricing 
                    WHERE type = 'domainrenew' 
                    AND currency = (SELECT id FROM tblcurrencies WHERE code = 'EUR')
                    AND relid = (SELECT id FROM tbldomainpricing WHERE extension = '." . $tld . "')
                )
                WHERE id = $domain->id
            ");
        }

        return $affectedRows;
    }

    /**
     * Updates domain pricing auto registrar (tbldomainpricing table)
     *
     * @param string $extension Extension (TLD)
     * @param string $registrar Registrar
     *
     * @return int Number of affected rows (How many domain pricings have been updated)
     */
    public function updateTldRegistrar($extension = '', $registrar = '')
    {
        return Capsule::table('tbldomainpricing')->where('extension', $extension)->update(['autoreg' => $registrar]);
    }

    /**
     * Reorder domain pricings alphabetically (tbldomainpricing table)
     *
     * @return void
     */
    public function reorderTlds()
    {
        $tlds = $this->getDomainPricingsForSelect();

        $i = 1;
        foreach ($tlds as $tld) {
            Capsule::table('tbldomainpricing')->where('extension', $tld)->update(['order' => $i]);
            $i++;
        }
    }

    /**
     * Inserts new domain pricing (TLD)
     *
     * @param \WHMCS\Module\Addon\Dondominio\Models\Pricing_Model $ddPricing TLD built from Dondonominio Addon Model
     *
     * @return void
     */
    public function insertDomainPricing(Pricing_Model $ddpricing)
    {
        $domainPricing = Capsule::table('tbldomainpricing')->where('extension', $ddpricing->tld)->first();

        if (!is_null($domainPricing)) {
            throw new Exception('tld_already_exists');
        }

        $whmcsVersion = $this->getConfiguration('version');

        if (version_compare($whmcsVersion, '7.6.0', '>=')) {
            $fields = [
                'extension' => $ddpricing->tld,
                'dnsmanagement' => 0,
                'emailforwarding' => 0,
                'idprotection' => 0,
                'eppcode' => $ddpricing->authcode_required ? 1 : 0,
                'autoreg' => 'dondominio',
                'order' => 0,
                'group' => 'none',
                'grace_period' => -1,
                'grace_period_fee' => '0.00',
                'redemption_grace_period' => -1,
                'redemption_grace_period_fee' => '0.00',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
        } else if (version_compare($whmcsVersion, '7.0.0', '>=')) {
            $fields = [
                'extension' => $ddpricing->tld,
                'dnsmanagement' => '',
                'emailforwarding' => '',
                'idprotection' => '',
                'eppcode' => $ddpricing->authcode_required ? 1 : 0,
                'autoreg' => 'dondominio',
                'order' => 0,
                'group' => 'none'
            ];
        } else {
            $fields = [
                'extension' => $ddpricing->tld,
                'dnsmanagement' => '',
                'emailforwarding' => '',
                'idprotection' => '',
                'eppcode' => $ddpricing->authcode_required ? 1 : 0,
                'autoreg' => 'dondominio',
                'order' => 0
            ];
        }

        Capsule::table('tbldomainpricing')->insert($fields);
    }
}