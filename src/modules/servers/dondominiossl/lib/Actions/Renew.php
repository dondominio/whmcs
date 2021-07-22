<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class Renew extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    /**
     * Renew a SSL Certificate
     *
     * @return stirng 'success' or error
     */
    public function execute(): string
    {
        $certificate = $this->getCertificateIDCustomFieldValue();
        $certificateID = $certificate->value;

        try {
            $this->checkParams();
            $info = $this->getCertificateInfo();

            if (!$info->get('renewable')) {
                return 'The certificate cannot be renewable';
            }

            $args = $this->getArgs();
            $args['csrData'] = $info->get('sslCert');
            $args['keyData'] = $info->get('sslKey');

            $this->addAltNames($args, $info);

            $this->api->renewCertificate($certificateID, $this->getArgs());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return 'success';
    }

    /**
     * Add alternative names to args
     *
     * @param array $args args for renew
     * @param \Dondominio\API\Response\Response $response Certificate API info response
     * 
     * @return void
     */
    protected function addAltNames(array &$args, \Dondominio\API\Response\Response $response): void
    {
        $sanMaxDomains = (int) $response->get('sanMaxDomains');
        $commonName = $response->get('commonName');
        $altNames = $response->get('alternativeNames');

        if (!is_array($altNames)) {
            return;
        }

        $altNames = array_values($altNames);

        for ($i = 0; $i < count($altNames); $i++) {
            $altKey = $i + 1;

            if ($commonName === [$altNames[$i]] || $altKey > $sanMaxDomains) {
                continue;
            }

            $args['alt_name_' . $altKey] = $altNames[$i];
            $args['alt_validation_' . $altKey] = 'dns';
        }
    }
}
