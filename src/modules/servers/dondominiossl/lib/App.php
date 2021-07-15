<?php

namespace WHMCS\Module\Server\Dondominiossl;


class App
{
    const DEFAULT_LANG = 'spanish';

    protected array $params = [];
    protected ?\WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api = null;

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getLanguage(): \WHMCS\Module\Server\Dondominiossl\Lang\Translations
    {
        $lang = static::DEFAULT_LANG;

        if (isset($this->getParams()['templatevars']['loggedinuser'])) {
            $lang = $this->getParams()['templatevars']['loggedinuser']->language;
        }

        $class = sprintf('\WHMCS\Module\Server\Dondominiossl\Lang\%s', ucfirst($lang));

        if (class_exists($class)) {
            $trans = new $class();

            if ($trans instanceof \WHMCS\Module\Server\Dondominiossl\Lang\Translations) {
                return $trans;
            }
        }

        return new \WHMCS\Module\Server\Dondominiossl\Lang\Spanish();
    }

    /**
     * Return an implementation of APIService_Interface
     *
     * @return \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
     */
    public function getApiService(): \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
    {
        if (is_null($this->api)) {
            $config = [
                'apiuser' => \WHMCS\Database\Capsule::table('mod_dondominio_settings')->where(['key' => 'api_username'])->value('value'),
                'apipasswd' => base64_decode(\WHMCS\Database\Capsule::table('mod_dondominio_settings')->where(['key' => 'api_password'])->value('value')),
            ];
            $this->api = new \WHMCS\Module\Server\Dondominiossl\Services\API_Service($config);
        }

        return $this->api;
    }

    public function getCertificateInfo(string $infoType = 'ssldata', string $password = ''): ?\Dondominio\API\Response\Response
    {
        try {
            $api = $this->getApiService();
            $certificateID = $this->getParams()['customfields'][\WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID];
            $response = $api->getCertificateInfo($certificateID, $infoType, $password);
        } catch (\Exception $e) {
            throw $e;
            return null;
        }

        return $response;
    }

    /**
     * Process a WHMCS order for a product with the dondominiossl module
     *
     * @return string 'success' or error
     */
    public function createAccount(): string
    {
        $creator = new \WHMCS\Module\Server\Dondominiossl\Actions\CreateAccount($this->getApiService(), $this->getParams());
        return $creator->execute();
    }

    /**
     * Renew a WHMCS order for a product with the dondominiossl module
     *
     * @return string 'success' or error
     */
    public function renew(): string
    {
        $renew = new \WHMCS\Module\Server\Dondominiossl\Actions\Renew($this->getApiService(), $this->getParams());
        return $renew->execute();
    }

    /**
     * Reissue a WHMCS order for a product with the dondominiossl module
     *
     * @return string 'success' or error
     */
    public function reissue(array $csrDataArgs, string $validationMethod, array $altNames = [], array $altValidations = []): string
    {
        $reissue = new \WHMCS\Module\Server\Dondominiossl\Actions\Reissue($this->getApiService(), $this->getParams());
        $reissue->setCsrDataArgs($csrDataArgs);
        $reissue->setAltNames($altNames, $altValidations);
        $reissue->setValidationMethod($validationMethod);
        return $reissue->execute();
    }

    /**
     * Change a WHMS product certificate domain validation method
     *
     * @return string 'success' or error
     */
    public function changeValidationMethod(string $domain, string $method): string
    {
        $changeValidationMethod = new \WHMCS\Module\Server\Dondominiossl\Actions\ChangeValidationMethod($this->getApiService(), $this->getParams());
        $changeValidationMethod->setDomains($domain);
        $changeValidationMethod->setMethod($method);
        return $changeValidationMethod->execute();
    }

    /**
     * Resend validation mail of a WHMCS order for a product with the dondominiossl module
     *
     * @return string 'success' or error
     */
    public function resendValidationMail(): string
    {
        $resendValidationMail = new \WHMCS\Module\Server\Dondominiossl\Actions\ResendValidationMail($this->getApiService(), $this->getParams());
        return $resendValidationMail->execute();
    }
}
