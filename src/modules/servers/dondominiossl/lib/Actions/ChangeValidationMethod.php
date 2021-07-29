<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class ChangeValidationMethod extends \WHMCS\Module\Server\Dondominiossl\Actions\Base
{
    protected string $domain = '';
    protected string $method = '';

    /**
     * Set the domain for change the validation method
     * 
     * @return void
     */
    public function setDomains(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Set the validation method
     * 
     * @return void
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * Change validaton method of SSL Certificate
     *
     * @return stirng 'success' or error
     */
    public function execute(): string
    {
        $certificate = $this->getCertificateIDCustomFieldValue();
        $certificateID = $certificate->value;

        try {
            $info = $this->getCertificateInfo();

            if (!in_array($info->get('status'), ['process', 'reissue'])) {
                return 'The domain validation can\'t be changed';
            }

            $this->api->changeValidationMethod($certificateID, $this->domain, $this->method);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return 'success';
    }
}
