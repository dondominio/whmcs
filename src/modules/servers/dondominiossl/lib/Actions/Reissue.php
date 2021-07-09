<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class Reissue extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    protected array $csrDataArgs = [];

    public function setCsrDataArgs(array $csrDataArgs): void
    {
        $this->csrDataArgs = $csrDataArgs;
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

            if ($info->get('status') !== 'process') {
                return 'The certificate cannot be reissued';
            }
            
            $csrResponse = $this->createCSRData();

            $args = [
                'csrData' => $csrResponse->get('csrData'),
                'keyData' => $csrResponse->get('csrKey'),
            ];

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
