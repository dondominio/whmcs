<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class ResendValidationMail extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{

    /**
     * Resend validation mail of a SSL Certificate
     *
     * @return stirng 'success' or error
     */
    public function execute(): string
    {
        $certificate = $this->getCertificateIDCustomFieldValue();
        $certificateID = $certificate->value;
        $commonName = $this->params['domain'];

        try {
            $this->checkParams();
            $info = $this->getCertificateInfo();

            if ($info->get('status') !== 'process') {
                return 'Can\'t resend validation mail if certificate is not in process';
            }

            $this->api->resendValidationMail($certificateID, $commonName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return 'success';
    }
}
