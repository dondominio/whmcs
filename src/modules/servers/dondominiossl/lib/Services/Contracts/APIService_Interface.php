<?php

namespace WHMCS\Module\Server\Dondominiossl\Services\Contracts;

interface APIService_Interface
{
    public function getProductList(array $args = []): array;
    public function createCSRData(array $args): \Dondominio\API\Response\Response;
    public function createCertificate(int $productID, array $args): \Dondominio\API\Response\Response;
    public function renewCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response;
    public function getCertificateInfo(int $certificateID): \Dondominio\API\Response\Response;
}