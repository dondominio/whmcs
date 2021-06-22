<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface SSLService_Interface
{
    public function apiSync(): void;
    public function getProduct(int $id): \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model;
    public function getProductGroups(): array;
}
