<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface WatchlistService_Interface
{
    public function findWatchlistsOrderedByTld();
    public function updateWatchlist(array $tlds);
    public function tldIsWatchlisted($tld);
}