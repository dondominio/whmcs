<?php

namespace WHMCS\Module\Server\Dondominiossl\Services;

class API_Service implements \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
{
    protected $api;

    /**
     * Sets API attribute
     *
     * @param array $options API Options
     */
    public function __construct(array $options = [])
    {
        $this->api = new \WHMCS\Module\Server\Dondominiossl\Helpers\API($options);
    }

    /**
     * Gets App
     *
     * @return WHMCS\Module\Server\Dondominio\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Gets API attribute
     *
     * @return WHMCS\Module\Server\Dondominio\Helpers\API
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Retrieves API Connection
     *
     * @return \Dondominio\API\API
     */
    public function getApiConnection()
    {
        return $this->getApi()->getConnection();
    }

    /**
     * Collect a list of products from DonDominio API
     *
     * @return array
     */
    public function getProductList(array $args = []): array
    {
        $connection = $this->getApiConnection();

        try {
            $response = $connection->ssl_productList($args);
            $products = json_decode($response->getResponseData(), true);
        } catch (\Exception $e) {
            return [];
        }

        if (isset($products['products'])) {
            return $products['products'];
        }

        return [];
    }

    /**
     * Send a request to DonDominio API for the creation of a CSR Data
     *
     * @return \Dondominio\API\Response\Response
     */
    public function createCSRData(array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_csrCreate($args);
    }

    /**
     * Send a request to DonDominio API for the creation of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function createCertificate(int $productID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_create($productID, $args);
    }

    /**
     * Get the information of a certificate from the DonDominio API
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getCertificateInfo(int $certificateID, string $infoType = 'ssldata', string $pfxpassword = ''): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_getInfo($certificateID, [
            'infoType' => $infoType,
            'pfxpass' => $pfxpassword
        ]);
    }

    /**
     * Send a request to DonDominio API for the renew of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function renewCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_renew($certificateID, $args);
    }

    /**
     * Send a request to DonDominio API for the reissue of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function reissueCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_reissue($certificateID, $args);
    }

    /**
     * Send a request to DonDominio API for change the validation method of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function changeValidationMethod(int $certificateID, string $commonName, string $validationMethod): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_changeValidationMethod([
            'certificateID' => $certificateID,
            'commonName' => $commonName,
            'validationMethod' => $validationMethod,
        ]);
    }

    /**
     * Send a request to DonDominio API for resend the validation mail of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function resendValidationMail(int $certificateID, string $commonName): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_resendValidationMail($certificateID, $commonName);
    }

    /**
     * Send a request to DonDominio API for get the validation mails of a common name
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getValidationEmails(string $commonName, bool $includeAlternativeMethods = false): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_getValidationEmails($commonName, ['includeAlternativeMethods' => $includeAlternativeMethods]);
    }
}
