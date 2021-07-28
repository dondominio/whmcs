<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class Reissue extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    protected array $csrDataArgs = [];
    protected string $validationMethod = '';
    protected array $altNames = [];
    protected array $altValidations = [];

    /**
     * Set the args to generate the CSR
     * 
     * @return void
     */
    public function setCsrDataArgs(array $csrDataArgs): void
    {
        $this->csrDataArgs = $csrDataArgs;
        $this->csrDataArgs['commonName'] = $this->params['customfields'][$this->fieldCommonName];
    }

    /**
     * Set the common name validation method
     * 
     * @return void
     */
    public function setValidationMethod(string $validationMethod): void
    {
        $this->validationMethod = $validationMethod;
    }

    /**
     * Set the alternative names of certificate
     * 
     * @return void
     */
    public function setAltNames(array $altNames, array $altValidations): void
    {
        $this->altNames = $altNames;
        $this->altValidations = $altValidations;
    }

    /**
     * Reissue a SSL Certificate
     *
     * @return stirng 'success' or error
     */
    public function execute(): string
    {
        $certificate = $this->getCertificateIDCustomFieldValue();
        $certificateID = $certificate->value;

        try {
            $info = $this->getCertificateInfo();

            if ($info->get('status') !== 'valid') {
                return 'The certificate cannot be reissued';
            }

            $csrResponse = $this->createCSRData();

            $args = [
                'csrData' => $csrResponse->get('csrData'),
                'keyData' => $csrResponse->get('csrKey'),
                'validationMethod' => $this->validationMethod,
            ];

            $this->addAlternativeNames($args, $info);

            $this->api->reissueCertificate($certificateID, $args);
            $this->resetCertificateRenew($certificateID);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

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
        return $this->api->createCSRData($this->csrDataArgs);
    }

    /**
     * Add alternative names to args
     *
     * @param array $args args for renew
     * @param \Dondominio\API\Response\Response $response Certificate API info response
     * 
     * @return void
     */
    protected function addAlternativeNames(array &$args, \Dondominio\API\Response\Response $response): void
    {
        $product = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::where(['dd_product_id' => $this->params['configoption1']])->first();
        $numDomains = (int) $response->get('numDomains');
        $altNamesFiltred = [];
        $altValidationsFiltred = [];

        if ($numDomains <= 1 || !$product->isMultiDomain()) {
            return;
        }

        foreach ($this->altNames as $key => $val) {
            if (!empty($val) && !empty($this->altValidations[$key])) {
                $altNamesFiltred[] = $this->altNames[$key];
                $altValidationsFiltred[] =  $this->altValidations[$key];
            }
        }

        $namesCount = count($altNamesFiltred);
        $validationsCount = count($altValidationsFiltred);

        for ($i = 1; $i <= $namesCount && $i <= $validationsCount && $i <= $numDomains; $i++) {
            $args['alt_name_' . $i] = $altNamesFiltred[$i - 1];
            $args['alt_validation_' . $i] = $altValidationsFiltred[$i - 1];
        }
    }
}
