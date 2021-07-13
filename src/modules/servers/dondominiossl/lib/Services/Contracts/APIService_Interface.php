<?php

namespace WHMCS\Module\Server\Dondominiossl\Services\Contracts;

interface APIService_Interface
{
    public function getProductList(array $args = []): array;
    public function createCSRData(array $args): \Dondominio\API\Response\Response;
    public function createCertificate(int $productID, array $args): \Dondominio\API\Response\Response;
    public function renewCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response;
    public function reissueCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response;
    public function getCertificateInfo(int $certificateID, string $infoType = 'ssldata'): \Dondominio\API\Response\Response;
    public function changeValidationMethod(int $certificateID, string $domain, string $method): \Dondominio\API\Response\Response;
    public function resendValidationMail(int $certificateID, string $domain): \Dondominio\API\Response\Response;
}