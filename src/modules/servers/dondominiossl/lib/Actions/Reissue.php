<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class Reissue extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    protected array $csrDataArgs = [];
    protected string $validationMethod = '';
    protected array $altNames = [];
    protected array $altValidations = [];

    public function setCsrDataArgs(array $csrDataArgs): void
    {
        $this->csrDataArgs = $csrDataArgs;
        $this->csrDataArgs['commonName'] = $this->params['customfields'][$this->fieldCommonName];
    }

    public function setValidationMethod(string $validationMethod): void
    {
        $this->validationMethod = $validationMethod;
    }

    public function setAltNames(array $altNames, array $altValidations): void
    {
        $altNamesFiltred = [];
        $altValidationsFiltred = [];

        foreach ($altNames as $key => $val) {
            if (!empty($val)) {
                $altNamesFiltred[] = $altNames[$key];
                $altValidationsFiltred[] = $altValidations[$key];
            }
        }

        $namesCount = count($altNamesFiltred);
        $validationsCount = count($altValidationsFiltred);

        for ($i = 1; $i <= $namesCount && $i <= $validationsCount; $i++) {
            $this->altNames['alt_name_' . $i] = $altNamesFiltred[$i - 1];
            $this->altValidations['alt_validation_' . $i] = $altValidationsFiltred[$i - 1];
        }
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

            if ($info->get('sanMaxDomains') > 0) {
                $args = array_merge($args, $this->altNames, $this->altValidations);
            }

            $this->api->reissueCertificate($certificateID, $args);
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
}
