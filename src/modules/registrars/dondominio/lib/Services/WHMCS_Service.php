<?php

namespace WHMCS\Module\Registrar\Dondominio\Services;

use WHMCS\Database\Capsule;
use Carbon\Carbon;
use WHMCS\Config\Setting;
use WHMCS\Domain\Domain;
use WHMCS\Module\Registrar\Dondominio\App;
use WHMCS\Module\Registrar\Dondominio\Services\Contracts\WHMCSService_Interface;
use Exception;

class WHMCS_Service implements WHMCSService_Interface
{
    /**
     * Sets apiHelper attribute
     * 
     * @param WHMCS\Module\Registrar\Dondominio\App App
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Gets App
     * 
     * @return WHMCS\Module\Registrar\Dondominio\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Return value from one row in `tblconfiguration`
     *
     * @param string $setting Setting to search 
     * @return mixed value of requested setting
     */
    public function getConfiguration($setting)
    {
        return Setting::getValue($setting);
    }

    /**
    * Find if user exists in the database.
    *
    * @param int $id Client Id
    * @return boolean
    */
    public function clientExistsById($id)
    {
        $user = Capsule::table('tblclients')->where('id', $id)->first();

        return !is_null($user);
    }

    /**
    * Check if a domain already exists.
    * Returns true if the domain exists, false otherwise.
    *
    * @param string $cname Domain
    * @return boolean
    */
    public function domainExists($cname)
    {
        $domain = Capsule::table('tbldomains')->where('domain', $cname)->first();

        return !is_null($domain);
    }

    /**
    * Check if a TLD exists and is configured to work with DonDominio.
    * Returns true if the TLD exists, false otherwise.
    *
    * @param string $tld TLD
    * @return boolean
    */
    public function tldExists($tld)
    {
        if (strpos($tld, ".") === false) {
            $tld = ".$tld";
        }

        $checkTLD = Capsule::table('tbldomainpricing')->where(['extension' => $tld, 'autoreg' => 'dondominio'])->first();

        return !is_null($checkTLD);
    }

    /**
    * Create a new order to hold the domains.
    * Returns the Order Id upon success, or the error from the database if failed.
    *
    * @param int $clientId
    * @return stdClass
    */
    public function createOrder($clientId)
    {
        $now = Carbon::now()->toDateTimeString();

        $insert = Capsule::table('tblorders')->insert([
            'ordernum' => 1,
            'userid' => $clientId,
            'contactid' => 0,
            'date' => $now,
            'amount' => '0.00',
            'invoiceid' => 0,
            'status' => 'Active',
            'notes' => 'Created automatically by DonDominio Registrar Module v' . App::VERSION . ' on ' . date('m-d-Y H:i:s')
        ]);

        if (!$insert) {
            throw new Exception('Error while creating order.');
        }

        return Capsule::table('tblorders')->latest('id')->first();
    }

    /**
    * Create a domain.
    * Returns true if the domain has been created, or the error from the database if failed.
    * @param integer $orderId Order that will hold the domain
    * @param string $cname Domain to create
    * @param string $tld TLD of the domain
    * @param string $tsExpir Date of expiration
    * @return boolean|string
    */
    public function createDomain($orderId, $clientId, $response)
    {
        $price = 0;

        $tld = $response->get('tld');

        if (strpos($tld, ".") === false) {
            $tld = ".$tld";
        }

        $domainPricing = Capsule::table('tbldomainpricing')->where('extension', $tld)->first();

        if (is_object($domainPricing)) {
            $pricing = Capsule::table('tblpricing')->where(['type' => 'domainrenew', 'relid' => $domainPricing->id])->first();

            if (is_object($pricing)) {
                $price = $pricing->msetupfee;
            }
        }

        $domain = new Domain();
        $domain->userid = $clientId;
        $domain->orderid = $orderId;
        $domain->type = 'Register';
        $domain->registrationdate = $response->get('tsCreate');
        $domain->domain = $response->get('name');
        $domain->firstpaymentamount = '0.00';
        $domain->recurringamount = $price;
        $domain->registrar = 'dondominio';
        $domain->registrationperiod = 1;
        $domain->expirydate = $response->get('tsExpir');
        $domain->subscriptionid = '';
        $domain->promoid = 0;
        $domain->status = 'Active';
        $domain->nextduedate = $response->get('tsExpir');
        $domain->nextinvoicedate = $response->get('tsExpir');
        $domain->additionalnotes = 'Created automatically by DonDominio Registrar Module v' . App::VERSION . ' on ' . date('m-d-Y H:i:s');
        $domain->synced = 0;

        $domain->save();

        return $domain;
    }

    /**
    * Find Client from email
    *
    * @param string $email Email
    * @return integer|string|boolean
    */
    public function findClientByEmail($email)
    {
        return Capsule::table('tblclients')->where('email', $email)->first();
    }

    /**
     * Retrieve custom fields from DDBB by type
     * 
     * @param string $type Type of custom fields
     * @return array
     */
    public function getCustomFieldsByType($type)
    {
        return Capsule::table('tblcustomfields')->where('type', $type)->orderBy('fieldname')->pluck('fieldname');
    }

    /**
     * Retrieve custom fields from DDBB by type
     * 
     * @param string $type Type of custom fields
     * @return array
     */
    public function getCustomFieldsByFieldName($fieldname)
    {
        return Capsule::table('tblcustomfields')->where('fieldname', $fieldname)->first();
    }

    /**
     * Retrieve custom fields from DDBB by customer email
     * 
     * @param string $type Type of custom fields
     * @param string $email Email of client
     * @return array
     */
    public function getCustomFieldsValueByEmail($fieldname, $email)
    {
        $result = Capsule::selectOne('
            SELECT
                value
            FROM tblcustomfieldsvalues
            WHERE
                fieldid = (SELECT id FROM tblcustomfields WHERE fieldname = ?)
                AND relid = (SELECT id FROM tblclients WHERE email = ?)',
            [$fieldname, $email]
        );

        return is_null($result) ? $result : $result->value;
    }
}
