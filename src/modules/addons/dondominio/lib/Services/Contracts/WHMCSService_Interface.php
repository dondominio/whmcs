<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

use WHMCS\Domain\Domain;
use WHMCS\Module\Addon\Dondominio\Models\Pricing_Model;

interface WHMCSService_Interface
{
    public function getCurrency($currency);
    public function getConfiguration($setting);
    public function getDomains(array $filters = [], $offset = null, $limit = null);
    public function getDomainsCount(array $filters);
    public function getDomainPricingsForSelect();
    public function insertPricing($type, $tld_id, $currency_id);
    public function getDisctintRegistrars();
    public function getDomainById($id);
    public function getDomain(array $where);
    public function syncDomain(Domain $domain);
    public function switchRegistrar(Domain $domain, $registrar, $status = null);
    public function updateRecurringPrice(Domain $domain);
    public function transferDomain(Domain $domain, $authCode = '');
    public function getClients();
    public function insertOrderWithUserId($userid);
    public function importDomain($apiDomainId, $customerId, $orderId);
    public function getDomainPricingsCount();
    public function getDomainPricings(array $filters = [], $offset = null, $limit = null);
    public function getDomainPricing(array $filters = []);
    public function insertPricingsForOtherCurrencies();
    public function savePricingsForEur(Pricing_Model $tld);
    public function updatePricingsForOtherCurrencies();
    public function updateDomainPrices();
    public function updateTldRegistrar($extension = '', $registrar = '');
    public function reorderTlds();
    public function insertDomainPricing(Pricing_Model $ddpricing);
}