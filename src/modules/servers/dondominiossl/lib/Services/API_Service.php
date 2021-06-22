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

    public function createCertificate(array $args): \Dondominio\API\Response\Response
    {
        $connection = $this->getApiConnection();
        return $connection->ssl_create($args);
    }

}