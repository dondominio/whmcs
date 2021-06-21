<?php

namespace WHMCS\Module\Server\Dondominiossl\Services\Contracts;

interface APIService_Interface
{
    public function createCertificate(array $args): array;
    public function getProductList(array $args = []): array;
}