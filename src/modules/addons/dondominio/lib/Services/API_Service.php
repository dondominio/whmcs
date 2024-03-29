<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\APIService_Interface;
use WHMCS\Module\Addon\Dondominio\Helpers\API;
use stdClass;
use Exception;

class API_Service extends AbstractService implements APIService_Interface
{
    const USER_NO_EXIST = 1003;
    const INVALID_PASSWORD = 1004;

    protected $api;

    /**
     * Sets API attribute
     *
     * @param array $options API Options
     */
    public function __construct(array $options = [])
    {
        $this->api = new API($options);
    }

    /**
     * Gets API attribute
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\API
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Sets new instance in apiHelper attributre
     *
     * @param string $apiOptions API Options
     *
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\API
     */
    public function reload($apiOptions = [])
    {
        $apiOptions = array_merge($this->getApi()->getApiOptions(), $apiOptions);

        $this->api = new API($apiOptions);

        return $this->api;
    }

    /**
     * Retrieves API Connection
     *
     * @return \Dondominio\API\API
     */
    public function getApiConnection()
    {
        return $this->getApi()->getConnection();
    }

    /**
     * Performs a 'hello' action against API
     * Checks if connection is OK
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#tool-hello
     *
     * @return \Dondominio\API\Response\Response
     */
    public function doHello()
    {
        $response = $this->getApiConnection()->tool_hello();

        return $this->parseResponse($response, []);
    }

    /**
     * Gets Domain Info
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#domain-getinfo
     *
     * @param string $domain Domain name or Domain ID
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getDomainInfo($domain, $infoType = 'status')
    {
        $params = ['infoType' => $infoType];
        $response = $this->getApiConnection()->domain_getInfo($domain, $params);

        $paramsToLog = ['domain' => $domain, 'params' => $params];
        return $this->parseResponse($response, $paramsToLog);
    }

    /**
     * Gets Domains List
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#domain-getlist
     *
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getDomainList($page = null, $pageLength = null, $word = null, $tld = null)
    {
        $params = [];

        if (!is_null($page)) {
            $params['page'] = $page;
        }

        if (!is_null($pageLength)) {
            $params['pageLength'] = $pageLength;
        }

        if (!empty($pageLength)) {
            $params['word'] = $word;
        }

        if (!empty($pageLength)) {
            $params['tld'] = $tld;
        }

        $response = $this->getApiConnection()->domain_list($params);

        return $this->parseResponse($response, $params);
    }

    /**
     * Updates contact
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#domain-updatecontacts
     *
     * @param string $domain Domain name or Domain ID
     * @param string $type Type of contact
     * @param string $ddid Contact ID
     *
     * @return \Dondominio\API\Response\Response
     */
    public function updateContact($domain, $type, $ddid)
    {
        $response = $this->getApiConnection()->domain_updateContacts($domain, [$type . 'ContactID' => $ddid]);

        $paramsToLog = ['domain' => $domain, 'params' => [$type . 'ContactID' => $ddid]];
        return $this->parseResponse($response, $paramsToLog);
    }

    /**
     * Sends domain transfer petition
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#domain-transfer
     *
     * @param stdClass $extDomain Domain from DDBB
     * @param string $authCode Auth Code (EEP Code)
     * @param array $clientDetails Client Details (see WHMCS localAPI() 'getclientsdetails')
     *
     * @throws \Exception If transfer is not valid
     *
     * @return \Dondominio\API\Response\Response
     */
    public function transferDomain(stdClass $extDomain, $authCode, array $clientDetails)
    {
        /*
        * Parsing Organization Type
        */
        $orgType = static::getCodeFromVatNumber($extDomain->vatnumber);

        /*
		 * Building parameter array for DonDominio's API
		 */
        $params = [
            'nameservers' => 'keepns',
            'authcode' => $authCode,
            'ownerContactType' => ($orgType == "1" || $extDomain->country != 'ES') ? 'individual' : 'organization',
            'ownerContactFirstName' => $extDomain->firstname,
            'ownerContactLastName' => $extDomain->lastname,
            'ownerContactOrgName' => $extDomain->companyname,
            'ownerContactOrgType' => $orgType,
            'ownerContactIdentNumber' => $extDomain->vatnumber,
            'ownerContactEmail' => $extDomain->email,
            'ownerContactPhone' => '+' . $clientDetails['client']['phonecc'] . '.' . $clientDetails['client']['phonenumber'],
            'ownerContactAddress' => $extDomain->address1,
            'ownerContactPostalCode' => $extDomain->postcode,
            'ownerContactCity' => $extDomain->city,
            'ownerContactState' => $extDomain->state,
            'ownerContactCountry' => $clientDetails['client']['countrycode']
        ];

        $response = $this->getApiConnection()->domain_transfer($extDomain->domain, $params);

        $paramsToLog = ['domain' => $extDomain->domain, 'params' => $params];
        return $this->parseResponse($response, $paramsToLog);
    }

    /**
     * Checks domain
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#domain-check
     *
     * @param string $domain Domain Name or Domain ID
     *
     * @return \Dondominio\API\Response\Response
     */
    public function checkDomain($domain)
    {
        $response = $this->getApiConnection()->domain_check($domain);

        $paramsToLog = ['domain' => $domain];
        return $this->parseResponse($response, $paramsToLog);
    }

    /**
     * Get Account zones for TLD
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#account-zones
     *
     * @param array $params Filters
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getAccountZones($params)
    {
        $response = $this->getApiConnection()->account_zones($params);

        return $this->parseResponse($response, $params);
    }

    /**
     * Get Account Info
     *
     * @see https://dondominio.dev/api/docs/sdk-php/#info-account-info
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getAccountInfo()
    {
        $response = $this->getApiConnection()->account_info();

        return $this->parseResponse($response);
    }

    /**
     * Gets deleted domains list
     *
     * @see https://dondominio.dev/api/docs/api/#list-deleted-domain-listdeleted
     *
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getListDeleted($page = null, $pageLength = null)
    {
        $params = [];

        if (!is_null($page)) {
            $params['page'] = $page;
        }

        if (!is_null($page)) {
            $params['pageLength'] = $pageLength;
        }

        $response = $this->getApiConnection()->domain_listDeleted($params);

        return $this->parseResponse($response, $params);
    }

    /**
     * Gets history of a domain
     *
     * @see https://dondominio.dev/api/docs/api/#get-history-domain-gethistory
     *
     * @param string $domain Domain from which you want to obtain the history
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getDomainHistory($domain, $page = null, $pageLength = null)
    {
        $params = [];

        if (!is_null($page)) {
            $params['page'] = $page;
        }

        if (!is_null($page)) {
            $params['pageLength'] = $pageLength;
        }

        $response = $this->getApiConnection()->domain_getHistory($domain, $params);

        return $this->parseResponse($response, $params);
    }

    /**
     * Gets the contacts of your account
     *
     * @see https://dondominio.dev/api/docs/api/#list-contact-list
     *
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getContactList($page = null, $pageLength = null, $name = null, $email = null, $verification = null, $daaccepted = null)
    {
        $params = [
            'page' => $page,
            'pageLength' => $pageLength,
            'name' => $name,
            'email' => $email,
            'verificationstatus' => $verification,
            'daaccepted' => $daaccepted,
        ];

        foreach ($params as $key => $param){
            if(is_null($param)){
                unset($params[$key]);
            }
        }

        $response = $this->getApiConnection()->contact_getList($params);

        return $this->parseResponse($response, $params);
    }

    /**
     * Gets the contacts of your account
     *
     * @see https://dondominio.dev/api/docs/api/#get-info-contact-getinfo
     *
     * @param int $contactID ID of contact
     * @param int $infoType Type of information
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getContactInfo($contactID, $infoType = 'data')
    {
        $response = $this->getApiConnection()->contact_getInfo($contactID, $infoType);

        return $this->parseResponse($response, ['contactID' => $contactID, 'infoType' => $infoType,]);
    }

    /**
     *  Resend the contact details verification email
     *
     * @see https://dondominio.dev/api/docs/api/#esend-verification-mail-contact-resendverificationmail
     *
     * @param int $page Offset where query starts
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getContactResendVerificationMail($contactID)
    {
        $response = $this->getApiConnection()->contact_resendVerificationMail($contactID);

        return $this->parseResponse($response, ['contactID' => $contactID]);
    }

    /**
     * Gets the SSL Products
     *
     * @see https://dondominio.dev/api/docs/api/#ssl-product-list-ssl-productlist
     *
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getSSLProductList($page = null, $pageLength = null)
    {
        $params = [
            'page' => $page,
            'pageLength' => $pageLength,
        ];

        foreach ($params as $key => $param) {
            if (is_null($param)) {
                unset($params[$key]);
            }
        }

        $response = $this->getApiConnection()->ssl_productList($params);

        return $this->parseResponse($response, $params);
    }

    /**
     * Gets the SSL Certificates
     *
     * @see https://dondominio.dev/api/docs/api/#ssl-list-ssl-list
     *
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     * @param array $filters Filters
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getSSLCertificates($page = null, $pageLength = null, $filters = [])
    {
        $filters['page'] = $page;
        $filters['pageLength'] = $pageLength;

        foreach ($filters as $key => $param) {
            if (is_null($param)) {
                unset($filters[$key]);
            }
        }

        $response = $this->getApiConnection()->ssl_list($filters);

        return $this->parseResponse($response, $filters);
    }

    /**
     * Gets the SSL Certificates
     *
     * @see https://dondominio.dev/api/docs/api/#ssl-list-ssl-list
     *
     * @param int $page Offset where query starts
     * @param int $pageLength Limit where query ends
     * @param array $filters Filters
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getSSLCertificateInfo($certificateID, $infoType = 'ssldata')
    {
        $response = $this->getApiConnection()->ssl_getInfo($certificateID, ['infoType' => $infoType]);

        return $this->parseResponse($response, ['certificateID' => $certificateID, 'infoType' => $infoType]);
    }

    /**
     * Send a request to DonDominio API for the creation of a CSR Data
     *
     * @return \Dondominio\API\Response\Response
     */
    public function createCSRData(array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_csrCreate($args);
    }

    /**
     * Send a request to DonDominio API for the renew of a certificate
     *
     * @param int $certificateID
     * @param array $args
     * 
     * @return \Dondominio\API\Response\Response
     */
    public function renewCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_renew($certificateID, $args);
    }

    /**
     * Send a request to DonDominio API for the reissue of a certificate
     *
     * @param int $certificateID
     * @param array $args
     * 
     * @return \Dondominio\API\Response\Response
     */
    public function reissueCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_reissue($certificateID, $args);
    }

    /**
     * Send a request to DonDominio API for resend the validation mail of a CommonName of a Certificate
     *
     * @param int $certificateID
     * @param string $commonName
     * 
     * @return \Dondominio\API\Response\Response
     */
    public function resendValidationMail(int $certificateID, string $commonName): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_resendValidationMail($certificateID, ['commonName' => $commonName]);
    }

    /**
     * Send a request to DonDominio API for Changes validation method for a CommonName
     *
     * @param int $certificateID
     * @param string $commonName
     * @param string $validationMethod
     * 
     * @return \Dondominio\API\Response\Response
     */
    public function changeValidationName(int $certificateID, string $commonName, string $validationMethod): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_changevalidationmethod($certificateID, [
            'commonName' => $commonName,
            'validationMethod' => $validationMethod
        ]);
    }

    /**
     * Send a request to DonDominio API for get the validation mails of a common name
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getValidationEmails(string $commonName, bool $includeAlternativeMethods = false): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_getValidationEmails($commonName, ['includeAlternativeMethods' => (int) $includeAlternativeMethods]);
    }

    public function printApiInfo()
    {
        $this->getApiConnection()->info();
    }

    /**
     * Log action and return response if valid
     * @see https://developers.whmcs.com/provisioning-modules/module-logging/
     *
     * @param \Dondominio\API\Response\Response $response API Response
     * @param array $params Params for logging
     *
     * @throws \Exception If response is not success
     *
     * @return \Dondominio\API\Response\Response 
     */
    public function parseResponse($response, array $params = [])
    {
        $errorCode = (int) $response->getErrorCode();
        $succes = 1;

        // Call internal WHMCS function logModuleCall
        if (function_exists('logModuleCall')) {
            logModuleCall($this->getApp()->getName(), $response->getAction(), $params, $response->getRawResponse(), $response->getArray());
        }

        if ($errorCode === static::USER_NO_EXIST || $errorCode == static::INVALID_PASSWORD){
            $succes = 0;
        }

        $this->getApp()->getService('settings')->setSetting('api_conexion', $succes);

        if (!$response->getSuccess()) {
            throw new Exception($response->getErrorCodeMsg(), $errorCode);
        }

        return $response;
    }

    /**
     * Check if API conexion is ok
     *
     * @return bool
     */
    public function checkConnection($checkApi)
    {
        $settingService = $this->getApp()->getService('settings');

        if ($checkApi) {

            try {
                $this->doHello();
            } catch (\Exception $e) {
                $settingService->setSetting('api_conexion', 0);
                throw $e;
            }

            $settingService->setSetting('api_conexion', 1);
            return true;
        }

        $apiConexion = $settingService->getSetting('api_conexion');

        return (bool) $apiConexion;
    }

    /**
     * Convert organization type to the corresponding code for the API using a VAT Number.
     *
     * @param string $vat VAT Number used to get the code
     *
     * @return string
     */
    public static function getCodeFromVatNumber($vatNumber)
    {
        $letter = substr($vatNumber, 0, 1);

        if (is_numeric($letter)) {
            return "1";
        }

        switch ($letter) {
            case 'A':
                return "524";
                break;
            case 'B':
                return "612";
                break;
            case 'C':
                return "560";
                break;
            case 'D':
                return "562";
                break;
            case 'E':
                return "150";
                break;
            case 'F':
                return "566";
                break;
            case 'G':
                return "47";
                break;
            case 'J':
                return "554";
                break;
            case 'P':
                return "747";
                break;
            case 'Q':
                return "746";
                break;
            case 'R':
                return "164";
                break;
            case 'S':
                return "436";
                break;
            case 'U':
                return "717";
                break;
            case 'V':
                return "877";
                break;
            case 'N':
            case 'W':
                return "713";
                break;
            case 'X':
            case 'Y':
            case 'Z':
                return "1";
        }

        return "877";
    }
}
