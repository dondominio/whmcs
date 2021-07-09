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

            $this->api->renewCertificate($certificateID, $this->getArgs());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return 'success';
    }
}
