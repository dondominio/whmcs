<?php

namespace WHMCS\Module\Server\Dondominiossl\Services\Contracts;

interface APIService_Interface
{
    public function getProductList(array $args = []): array;
    public function createCSRData(array $args): \Dondominio\API\Response\Response;
    public function createCertificate(int $productID, array $args): \Dondominio\API\Response\Response;
    public function renewCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response;
    public function reissueCertificate(int $certificateID, array $args): \Dondominio\API\Response\Response;
    public function getCertificateInfo(int $certificateID, string $infoType = 'ssldata', string $pfxpassword = ''): \Dondominio\API\Response\Response;
    public function changeValidationMethod(int $certificateID, string $commonName, string $method): \Dondominio\API\Response\Response;
    public function resendValidationMail(int $certificateID, string $commonName): \Dondominio\API\Response\Response;
    public function getValidationEmails(string $commonName, bool $includeAlternativeMethods = false): \Dondominio\API\Response\Response;
}