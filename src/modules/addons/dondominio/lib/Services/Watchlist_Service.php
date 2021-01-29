<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\WatchlistService_Interface;
use WHMCS\Module\Addon\Dondominio\Models\Watchlist_Model;

class Watchlist_Service extends AbstractService implements WatchlistService_Interface
{
    /**
     * Get watchlist order by tld
     * 
     * @return \Illuminate\Support\Collection
     */
    public function findWatchlistsOrderedByTld()
    {
        return Watchlist_Model::orderBy('tld', 'asc')->pluck('tld');
    }

    /**
     * Clean and insert Tlds in watchlist table
     *
     * @param array $tlds Array of tlds to watch
     *
     * @return void
     */
    public function updateWatchlist(array $tlds)
    {
        $formatTlds = array_map(function($tld) {
            return ['tld' => $tld];
        }, $tlds);

        Watchlist_Model::truncate();
        Watchlist_Model::insert($formatTlds);
    }

    /**
     * Gets if extension (tld) is watchlisted
     *
     * @param string $tld Extension (TLD)
     *
     * @return bool
     */
    public function tldIsWatchlisted($tld)
    {
        $watchlistMode = $this->getApp()->getService('settings')->getSetting('watchlist_mode');

        if ($watchlistMode == "disable") {
            return true;
        }

        if (strpos($tld, ".") === false) {
            $tld = ".$tld";
        }

        $watchlist = Watchlist_Model::where(['tld' => $tld])->first();

        if (
            ($watchlistMode == 'watch' && !is_null($watchlist))
            || ($watchlistMode == 'ignore' && is_null($watchlist))
        ) {
            return true;
        }

        return false;
    }
}