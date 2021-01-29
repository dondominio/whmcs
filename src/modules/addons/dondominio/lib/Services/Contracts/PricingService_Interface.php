<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface PricingService_Interface
{
    public function findPricingsNotInWatchlist();
    public function findPricingsInDomainPricings();
    public function apiSync($initialSync = false);
    public function getCacheStatus();
    public function findPricingByTld($tld);
    public function getAvailableTlds(array $filters = [], $offset = null, $limit = null);
    public function getAvailableTldsCount(array $filters);
}