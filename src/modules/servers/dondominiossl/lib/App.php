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

    /**
     * Return the Translations with the actual lang
     *
     * @return \WHMCS\Module\Server\Dondominiossl\Lang\Translations
     */
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

    /**
     * Return the certificateinfo API Response
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getCertificateInfo(string $infoType = 'ssldata', string $password = ''): ?\Dondominio\API\Response\Response
    {
        $api = $this->getApiService();
        $certificateID = $this->getParams()['customfields'][\WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID];

        if (empty($certificateID)) {
            return null;
        }

        try {
            $response = $api->getCertificateInfo((int) $certificateID, $infoType, $password);
        } catch (\Exception $e) {
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
    public function resendValidationMail(string $commonName): string
    {
        $resendValidationMail = new \WHMCS\Module\Server\Dondominiossl\Actions\ResendValidationMail($this->getApiService(), $this->getParams());
        $resendValidationMail->setCommonName($commonName);
        return $resendValidationMail->execute();
    }

    /**
     * Return array with the common name validation mails
     *
     * @return null|array
     */
    public function getCommonNameValidationEmails(string $commonName, bool $includeAlternativeMethods = false): ?array
    {
        try {
            $response = $this->getApiService()->getValidationEmails($commonName, $includeAlternativeMethods);
        } catch (\Exception $e) {
            return null;
        }

        if (is_array($response->get('valMethods'))) {
            return $response->get('valMethods');
        }

        return [];
    }

    /**
     * Return array with the DonDominio SSL Products for create WHMCS Products 
     *
     * @return array
     */
    public function getProductSelect(): array
    {
        $products = $this->getApiService()->getProductList();
        $productOptions = [];

        foreach ($products as $p) {
            if (!$p['isTrial']) {
                $productOptions[$p['productID']] = $p['productName'];
            }
        }

        return $productOptions;
    }

    /**
     * Return the order invoice id 
     *
     * @return null|int
     */
    public function getInvoiceId(): ?int
    {
        $userId = $this->getParams()['userid'];
        $serviceId = $this->getParams()['serviceid'];

        $item = \WHMCS\Billing\Invoice\Item::where([
            'type' => 'Hosting',
            'relid' => $serviceId,
            'userid' => $userId,
        ])->first();

        if (!is_object($item)) {
            return null;
        }

        if (!empty($item->invoiceid)) {
            return (int) $item->invoiceid;
        }

        return null;
    }
}
