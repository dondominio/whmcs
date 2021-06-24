<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class CreateAccount
{
    protected array $params = [];
    protected \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api;

    protected string $fieldCommonName = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_COMMON_NAME;
    protected string $fieldCertificateID = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID;

    public function __construct(
        \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api,
        array $params
    ) {
        $this->api = $api;
        $this->params = $params;
    }

    public function execute(): string
    {
        if (!strlen($this->params['configoption1'])){
            return 'Product ID not found';
        }

        if (!strlen($this->params['configoption2'])){
            return 'VAT Number not found';
        }

        if (!strlen($this->params['customfields'][$this->fieldCommonName])){
            return 'Common Name not found';
        }

        $customFieldValue = $this->getCertificateIDCustomFieldValue();

        if (!strlen($customFieldValue)){
            return sprintf('Custom Field %s not found', $this->fieldCertificateID);
        }

        try {
            $csr = $this->createCSRData();
        } catch (\Exception $e){
            return 'CSR Error: ' . $e->getMessage();
        }

        try {
            $address = $this->params['clientsdetails']['address1'];
            $address = strlen($address) ? $address : $this->params['clientsdetails']['address2'];

            $args = [
                'csrData' => $csr,
                'adminContactType' => 'individual',
                'adminContactFirstName' => $this->params['clientsdetails']['firstname'],
                'adminContactLastName' => $this->params['clientsdetails']['lastname'],
                'adminContactIdentNumber' => $this->getVATNumber(),
                'adminContactEmail' => $this->params['clientsdetails']['email'],
                'adminContactPhone' => $this->params['clientsdetails']['phonenumberformatted'],
                'adminContactFax' => $this->params['clientsdetails']['phonenumberformatted'],
                'adminContactAddress' => $address,
                'adminContactPostalCode' => $this->params['clientsdetails']['postcode'],
                'adminContactCity' => $this->params['clientsdetails']['city'],
                'adminContactState' => $this->params['clientsdetails']['state'],
                'adminContactCountry' => $this->params['clientsdetails']['countrycode'],
            ];

            $response = $this->api->createCertificate($this->params['configoption1'], $args);
        } catch (\Exception $e){
            return $this->getVATNumber() . ': ' . $e->getMessage();
        }

        $customFieldValue->value = $response->get('ssl')['certificateID'];
        $customFieldValue->save();

        $service = $this->params['model'];
        $service->overrideSuspendUntilDate = $response->get('ssl')['tsExpir'];
        $service->terminationDate = $response->get('ssl')['tsExpir'];   
        $service->save();

        return 'success';
    }

    protected function getCertificateIDCustomFieldValue()
    {
        $service = $this->params['model'];
        $customFieldValues = $service->customFieldValues;

        foreach ($customFieldValues as $value){
            $customField = $value->customField; 
         
            if ($customField->fieldName === $this->fieldCertificateID){
                return $value;
            }
        }

        return null;
    }

    protected function createCSRData(): string
    {
        $args = [
            'commonName' => $this->params['customfields'][$this->fieldCommonName],
            'organizationName' => $this->params['clientsdetails']['companyname'],
            'organizationalUnitName' => $this->params['clientsdetails']['companyname'],
            'countryName' => $this->params['clientsdetails']['countrycode'],
            'stateOrProvinceName' => $this->params['clientsdetails']['state'],
            'localityName' => $this->params['clientsdetails']['city'],
            'emailAddress' => $this->params['clientsdetails']['email'],
        ];

        $response = $this->api->createCSRData($args);
        return $response->get('csrData');
    }

    protected function getVATNumber(): string
    {
        $clientCustomFields = $this->params['clientsdetails']['customfields'];

        foreach ($clientCustomFields as $cf){
            if ($cf['id'] === $this->params['configoption2']){
                return $cf['value'];
            }
        }

        return '';
    }

}
