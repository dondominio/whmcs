<?php

namespace WHMCS\Module\Server\Dondominiossl\Services\Contracts;

interface APIService_Interface
{
    public function createCertificate(array $args): \Dondominio\API\Response\Response;
    public function getProductList(array $args = []): array;
}