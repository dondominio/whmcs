<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface SSLService_Interface
{
    public function apiSync(bool $updatePrices = false, int $page = 0): void;
    public function getProduct(int $id): ?\WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model;
    public function getProductGroups(): array;
    public function getCertificateOrder(int $certificateID): ?\WHMCS\Module\Addon\Dondominio\Models\SSLCertificateOrder_Model;
    public function updateCertificatesRenewDate(): void;
}
