<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class Renew extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    public function execute(): string
    {
        $certificate = $this->getCertificateIDCustomFieldValue();
        $certificateID = $certificate->value;

        try {
            $info = $this->getCertificateInfo();

            if (!$info->get('renewable')) {
                return 'The certificate cannot be renewable';
            }

            $address = $this->params['clientsdetails']['address1'];
            $address = strlen($address) ? $address : $this->params['clientsdetails']['address2'];

            $args = [
                'csrData' => $info->get('sslCert'),
                'keyData' => $info->get('sslKey'),
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

            $this->api->renewCertificate($certificateID, $args);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return 'success';
    }

    protected function getCertificateInfo(): \Dondominio\API\Response\Response
    {
        $certificateID = $this->params['customfields'][$this->fieldCertificateID];
        return $this->api->getCertificateInfo($certificateID);
    }
}
