<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class CreateAccount extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    public function execute(): string
    {
        if (strlen($this->params['customfields'][$this->fieldCertificateID])) {
            return 'success';
        }

        $customFieldValue = $this->getCertificateIDCustomFieldValue();

        if (!strlen($customFieldValue)) {
            return sprintf('Custom Field %s not found', $this->fieldCertificateID);
        }

        try {
            $this->checkParams();
            $csrResponse = $this->createCSRData();

            $args = $this->getArgs();
            $args['csrData'] = $csrResponse->get('csrData');
            $args['keyData'] = $csrResponse->get('csrKey');

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
