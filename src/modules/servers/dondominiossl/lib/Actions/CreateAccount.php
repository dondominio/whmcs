<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class CreateAccount extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    public function execute(): string
    {
        if (strlen($this->params['customfields'][$this->fieldCertificateID])) {
            return 'success';
        }

        if (!strlen($this->params['configoption1'])) {
            return 'Product ID not found';
        }

        if (!strlen($this->params['configoption2'])) {
            return 'VAT Number not found';
        }

        if (!strlen($this->params['customfields'][$this->fieldCommonName])) {
            return 'Common Name not found';
        }

        $customFieldValue = $this->getCertificateIDCustomFieldValue();

        if (!strlen($customFieldValue)) {
            return sprintf('Custom Field %s not found', $this->fieldCertificateID);
        }

        try {
            $address = $this->params['clientsdetails']['address1'];
            $address = strlen($address) ? $address : $this->params['clientsdetails']['address2'];
            $csrResponse = $this->createCSRData();

            $args = [
                'csrData' => $csrResponse->get('csrData'),
                'keyData' => $csrResponse->get('csrKey'),
                'period' => $this->getPeriod(),
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
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $customFieldValue->value = $response->get('ssl')['certificateID'];
        $customFieldValue->save();

        return 'success';
    }

    protected function createCSRData(): \Dondominio\API\Response\Response
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

        return $this->api->createCSRData($args);
    }
}
