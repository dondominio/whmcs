<?php

namespace WHMCS\Module\Server\Dondominiossl\Services;

class API_Service implements \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
{
    const SSL_MODULE_NAME = 'dondominiossl';

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
     * Create a Module Log in WHMCS
     *
     * @return \Dondominio\API\API
     */
    protected function logResponse(\Dondominio\API\Response\Response $response, array $params = []): void
    {
        // Call internal WHMCS function logModuleCall
        if (function_exists('logModuleCall')) {
            logModuleCall(static::SSL_MODULE_NAME, $response->getAction(), $params, $response->getRawResponse(), $response->getArray());
        }

        if (!$response->getSuccess()) {
            throw new \Exception($response->getErrorCodeMsg(), $response->getErrorCode());
        }
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
            $this->logResponse($response, $args);
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
        $response = $connection->ssl_csrCreate($args);
        $this->logResponse($response, $args);
        return $response;
    }

    /**
     * Send a request to DonDominio API for the creation of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function createCertificate(int $productID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $response = $connection->ssl_create($productID, $args);
        $this->logResponse($response, $args);
        return $response;
    }

    /**
     * Get the information of a certificate from the DonDominio API
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getCertificateInfo(int $certificateID, string $infoType = 'ssldata', string $pfxpassword = ''): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $args = [
            'infoType' => $infoType,
            'pfxpass' => $pfxpassword
        ];
        $response = $connection->ssl_getInfo($certificateID, $args);
        $this->logResponse($response, $args);
        return $response;
    }

    /**
     * Send a request to DonDominio API for the renew of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function renewCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $response = $connection->ssl_renew($certificateID, $args);
        $this->logResponse($response, $args);
        return $response;
    }

    /**
     * Send a request to DonDominio API for the reissue of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function reissueCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $response = $connection->ssl_reissue($certificateID, $args);
        $this->logResponse($response, $args);
        return $response;
    }

    /**
     * Send a request to DonDominio API for change the validation method of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function changeValidationMethod(int $certificateID, string $commonName, string $validationMethod): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $args = [
            'certificateID' => $certificateID,
            'commonName' => $commonName,
            'validationMethod' => $validationMethod,
        ];
        $response = $connection->ssl_changeValidationMethod($args);
        $this->logResponse($response, $args);
        return $response;
    }

    /**
     * Send a request to DonDominio API for resend the validation mail of a certificate
     *
     * @return \Dondominio\API\Response\Response
     */
    public function resendValidationMail(int $certificateID, string $commonName): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $response = $connection->ssl_resendValidationMail($certificateID, $commonName);
        $this->logResponse($response, ['commonName' => $commonName]);
        return $response;
    }

    /**
     * Send a request to DonDominio API for get the validation mails of a common name
     *
     * @return \Dondominio\API\Response\Response
     */
    public function getValidationEmails(string $commonName, bool $includeAlternativeMethods = false): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        $args = ['includeAlternativeMethods' => (int) $includeAlternativeMethods];
        $response = $connection->ssl_getValidationEmails($commonName, $args);
        $this->logResponse($response, ['commonName' => $commonName]);
        return $response;
    }
}
