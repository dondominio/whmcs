<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


abstract class Base
{
    protected array $params = [];
    protected \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api;

    protected string $fieldCertificateID = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID;
    protected string $fieldCommonName = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_COMMON_NAMEM;

    public function __construct(
        \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api,
        array $params
    ) {
        $this->api = $api;
        $this->params = $params;
    }

    public abstract function execute(): string;

    /**
     * Get the certificate ID in Custom Fields of the order
     * 
     * @return object|null
     */
    protected function getCertificateIDCustomFieldValue()
    {
        $service = $this->params['model'];
        $customFieldValues = $service->customFieldValues;

        foreach ($customFieldValues as $value) {
            $customField = $value->customField;

            if ($customField->fieldName === $this->fieldCertificateID) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Returns the customer's VAT Number
     * 
     * @return string
     */
    protected function getVATNumber(): string
    {
        $clientCustomFields = $this->params['clientsdetails']['customfields'];

        foreach ($clientCustomFields as $cf) {
            if ($cf['id'] === $this->params['configoption2']) {
                return $cf['value'];
            }
        }

        return '';
    }

    /**
     * Get years period of the product
     * 
     * @return int
     */
    protected function getPeriod(): int
    {
        $service = $this->params['model'];
        $billingCycle = $service->billingcycle;

        $yearsMap = [
            'Annually' => 1,
            'Biennially' => 2,
            'Triennially' => 3,
        ];

        return isset($yearsMap[$billingCycle]) ? $yearsMap[$billingCycle] : 1;
    }

    /**
     * Get base args for the create and renew requests
     * 
     * @return array
     */
    protected function getArgs(): array
    {
        return [
            'period' => $this->getPeriod(),
            'adminContactType' => 'individual',
            'adminContactFirstName' => $this->params['clientsdetails']['firstname'],
            'adminContactLastName' => $this->params['clientsdetails']['lastname'],
            'adminContactIdentNumber' => $this->getVATNumber(),
            'adminContactEmail' => $this->params['clientsdetails']['email'],
            'adminContactPhone' => $this->params['clientsdetails']['phonenumberformatted'],
            'adminContactAddress' => $this->getAddress(),
            'adminContactPostalCode' => $this->params['clientsdetails']['postcode'],
            'adminContactCity' => $this->params['clientsdetails']['city'],
            'adminContactState' => $this->params['clientsdetails']['state'],
            'adminContactCountry' => $this->params['clientsdetails']['countrycode'],
        ];
    }

    /**
     * Check that the necessary parameters are seted
     * 
     * @throws Exception if one parameter is not set
     *
     * @return void
     */
    protected function checkParams(?array $params = null, ?array $paramsToCheck = null): void
    {
        $paramsToCheck = is_null($params) && is_null($paramsToCheck) ? $this->getParamsToCheck() : $paramsToCheck;
        $params = is_null($params) ? $this->params : $params;

        foreach ($paramsToCheck as $paramKey => $paramMsg) {
            if (is_array($paramMsg)) {
                $this->checkParams($params[$paramKey], $paramMsg);
                continue;
            }

            if (!strlen($params[$paramKey])) {
                throw new \Exception($paramMsg);
            }
        }
    }

    /**
     * Returns a list with the necessary parameters for the creation and renewal requests to work
     *
     * @return array
     */
    protected function getParamsToCheck(): array
    {
        return [
            'configoption1' => 'Product ID not found',
            'configoption2' => 'VAT Number not found',
            'clientsdetails' => [
                'companyname' => 'User Company Name not found',
                'countrycode' => 'User Country Code not found',
                'state' => 'User State not found',
                'city' => 'User City not found',
                'email' => 'User Email not found',
                'phonenumberformatted' => 'User Phone not found',
                'postcode' => 'User Post Code not found',
                'firstname' => 'User First Name not found',
                'lastname' => 'User Last Name not found',
            ],
            'customfields' => [
                $this->fieldCommonName => 'Common Name not found'
            ]
        ];
    }

    /**
     * Returns the customer's address
     *
     * @throws Exception if the address is no found
     * 
     * 
     * @return string
     */
    protected function getAddress(): string
    {
        $address = $this->params['clientsdetails']['address1'];
        $address = strlen($address) ? $address : $this->params['clientsdetails']['address2'];

        if (!strlen($address)) {
            throw new \Exception('User Address not found');
        }

        return $address;
    }

    /**
     * Make a request to DonDominio API to get Certificate Info
     * 
     * @throws Exception if the CSR Data creation is not successful
     *
     * @return \Dondominio\API\Response\Response
     */
    protected function getCertificateInfo(): \Dondominio\API\Response\Response
    {
        $certificateID = $this->params['customfields'][$this->fieldCertificateID];
        return $this->api->getCertificateInfo($certificateID);
    }
}
