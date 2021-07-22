<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class CreateAccount extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{
    protected string $fieldAltName = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_ALT_NAME;

    /**
     * Create a SSL Certificate
     *
     * @return stirng 'success' or error
     */
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

            $this->addAltNames($args);

            $response = $this->api->createCertificate($this->params['configoption1'], $args);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        $customFieldValue->value = $response->get('ssl')['certificateID'];
        $customFieldValue->save();

        $sslCertificateOrder = new \WHMCS\Module\Addon\Dondominio\Models\SSLCertificateOrder_Model();
        $sslCertificateOrder->certificate_id = $response->get('ssl')['certificateID'];
        $sslCertificateOrder->tblhosting_id = $this->params['serviceid'];
        $sslCertificateOrder->dd_product_id = $this->params['configoption1'];
        $sslCertificateOrder->save();

        return 'success';
    }

    /**
     * Make a request to DonDominio API for the creation of a CSR Data
     * 
     * @throws Exception if the CSR Data creation is not successful
     *
     * @return \Dondominio\API\Response\Response
     */
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

    /**
     * Add alternative names to args
     *
     * @param array $args args for create
     * 
     * @return void
     */
    protected function addAltNames(array &$args): void
    {
        $product = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::where(['dd_product_id' => $this->params['configoption1']])->first();
        $sanMaxDomains = $product->getSanMaxDomains();
        $altNameCount = 1;

        if (!$product->isMultiDomain()) {
            return;
        }

        for ($i = 0; $i < $sanMaxDomains; $i++) {
            if (empty($this->params['customfields'][$this->fieldAltName . $i])) {
                continue;
            }

            $args['alt_name_' . $altNameCount] = $this->params['customfields'][$this->fieldAltName . $i];
            $args['alt_validation_' . $altNameCount] = 'dns';

            $altNameCount++;
        }
    }

}
