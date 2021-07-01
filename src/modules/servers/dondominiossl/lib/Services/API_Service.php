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
        } catch (\Exception $e){
            return [];
        }

        if (isset($products['products'])){
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
    public function getCertificateInfo(int $certificateID): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_getInfo($certificateID, ['infoType' => 'ssldata']);
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

}