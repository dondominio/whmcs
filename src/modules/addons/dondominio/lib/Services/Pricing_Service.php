<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use Carbon\Carbon;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Dondominio\Services\Contracts\PricingService_Interface;
use WHMCS\Module\Addon\Dondominio\Models\Pricing_Model;
use WHMCS\Module\Addon\Dondominio\Models\Watchlist_Model;

class Pricing_Service extends AbstractService implements PricingService_Interface
{
    /**
     * Return pricing not listed in watchlist
     *
     * @return WHMCS\Module\Addon\Dondominio\Models\Pricing
     */
    public function findPricingsNotInWatchlist()
    {
        // SELECT tld FROM mod_dondominio_pricing WHERE tld NOT IN (SELECT tld FROM mod_dondominio_watchlist) ORDER BY tld ASC

        return Pricing_Model::whereNotIn('tld', function ($query) {
                $query->select('tld')->from((new Watchlist_Model)->getTable())->get();
        })->orderBy('tld', 'asc')->pluck('tld');
    }

    /**
     * Retrieves dondominio pricings where domain pricings are configured to use with dondominio
     *
     * @return \Illuminate\Database\Eloquent\Collection of Pricing_Model
     */
    public function findPricingsInDomainPricings()
    {
        return Pricing_Model::select()
            ->whereRaw('tld IN (SELECT extension COLLATE utf8_unicode_ci FROM tbldomainpricing WHERE autoreg = "dondominio" AND extension = tld)')
            ->get();
    }

    /**
     * Sync pricings with API
     *
     * If it's initial sync, first it truncates all data
     * If notifications are enabled, sends email about changes
     *
     * @param bool $initialSync if it's initial sync (If true, it will remove all data before sync)
     *
     * @return void 
     */
    public function apiSync($initialSync = false)
    {
        if ($initialSync) {
            Pricing_Model::truncate();
        }

        $pricesArray = [];

        $i = 1;
        $total = 0;

        do{
            $params = ['pageLength' => 100, 'page' => $i];
            $prices = $this->getApp()->getService('api')->getAccountZones($params);

            $pricesArray = array_merge($pricesArray, $prices->get("zones"));

            $total = $prices->get( "queryInfo" )['total'];

            $i++;
        } while ($total > count($pricesArray));

        $added_to_db = [];
        $prices_updated = [];

        foreach ($pricesArray as $data) {
            $tld = "." . $data['tld'];
            $oldPricing = null;
            $pricing = $this->findPricingByTld($tld);

            if (is_null($pricing)) {
                $pricing = new Pricing_Model();
            } else {
                $oldPricing = clone $pricing;
            }

            $pricing->tld = $tld;
            $pricing->old_register_price = !is_null($oldPricing) ? $oldPricing->register_price : null;
            $pricing->old_transfer_price = !is_null($oldPricing) ? $oldPricing->transfer_price : null;
            $pricing->old_renew_price = !is_null($oldPricing) ? $oldPricing->renew_price : null;
            $pricing->last_update = Carbon::now()->toDateTimeString();
            $pricing->authcode_required = $data['authcodereq'] ? 1 : 0;

            $pricing->register_price = array_key_exists('create', $data) ? $data['create']['price'] : null;
            $pricing->register_range = array_key_exists('create', $data) ? $data['create']['years'] : null;

            $pricing->transfer_price = array_key_exists('transfer', $data) ? $data['transfer']['price'] : null;
            $pricing->transfer_range = array_key_exists('transfer', $data) ? $data['transfer']['years'] : null;

            $pricing->renew_price = array_key_exists('renew', $data) ? $data['renew']['price'] : null;
            $pricing->renew_range = array_key_exists('renew', $data) ? $data['renew']['years'] : null;

            $pricing->save();

            if (is_null($oldPricing)) {
                $added_to_db[$data['tld']] = $pricing;
            } else {
                $priceChanged =
                    ($pricing->register_price != $pricing->old_register_price) ||
                    ($pricing->transfer_price != $pricing->old_transfer_price) ||
                    ($pricing->renew_price != $pricing->old_renew_price);

                if ($priceChanged && $this->getApp()->getService('watchlist')->tldIsWatchlisted($data['tld'])) {
                    $prices_updated[$data['tld']] = $pricing;
                }
            }
        }

        $notificationNewTlds = $this->getApp()->getService('settings')->getSetting('notifications_new_tlds');

        if (!$initialSync && $notificationNewTlds && count($added_to_db) > 0) {
            $this->getApp()->getService('email')->sendNewTldsEmail($added_to_db);
        }

        $notificationPrices = $this->getApp()->getService('settings')->getSetting('notifications_prices');

        if (!$initialSync && $notificationPrices && count($prices_updated) > 0) {
            $this->getApp()->getService('email')->sendUpdatedTldsEmail($prices_updated);
        }
    }

    /**
     * Get pricing resume: last_update and tlds in cache
     *
     * @return stdClass
     */
    public function getCacheStatus()
    {
        //SELECT DATE_FORMAT(MAX(last_update), '%d/%m/%Y %H:%i:%S'), COUNT(id) FROM mod_dondominio_pricing

        return Capsule::table((new Pricing_Model)->getTable())->selectRaw("
            DATE_FORMAT(MAX(last_update), '%d/%m/%Y %H:%i:%S') as last_update, COUNT(id) as count
        ")->first();
    }

    /**
     * Find pricing by extension (tld)
     *
     * @param string $tld Extension (TLD)
     *
     * @return Pricing_Model|null
     */
    public function findPricingByTld($tld)
    {
        if (strpos($tld, ".") === false) {
            $tld = ".$tld";
        }

        return Pricing_Model::where(['tld' => $tld])->first();
    }

    /**
     * Retrieves domain pricings (TLDs) from Dondominio that are not in WHMCS system
     *
     * @param array $filters Filters
     * @param int $offset Where query starts
     * @param int $limit Where query ends
     *
     * @return \Illuminate\Database\Eloquent\Collection of Pricing Model
     */
    public function getAvailableTlds(array $filters = [], $offset = null, $limit = null)
    {
        $queryBuilder = Pricing_Model::select();
        $queryBuilder->whereRaw('tld NOT IN (SELECT extension COLLATE utf8_unicode_ci FROM tbldomainpricing)');

        if (array_key_exists('tld', $filters) && strlen($filters['tld']) > 0) {
            $queryBuilder->where('tld', 'like', '%' . $filters['tld'] . '%');
        }

        if (!is_null($offset)) {
            $queryBuilder->offset($offset);
        }

        if (!is_null($limit)) {
            $queryBuilder->limit($limit);
        }

        $queryBuilder->orderBy('tld', 'ASC');

        //var_dump($queryBuilder->toSql());

        return $queryBuilder->get();
    }

    /**
     * Retrieves counter for domain pricings (TLDs) from Dondominio that are not in WHMCS system
     *
     * @param aray $filters Filters
     *
     * @return int
     */
    public function getAvailableTldsCount(array $filters)
    {
        $queryBuilder = Pricing_Model::select();
        $queryBuilder->whereRaw('tld NOT IN (SELECT extension COLLATE utf8_unicode_ci FROM tbldomainpricing)');

        if (array_key_exists('tld', $filters) && strlen($filters['tld']) > 0) {
            $queryBuilder->where('tld', 'like', '%' . $filters['tld'] . '%');
        }

        //var_dump($queryBuilder->toSql());

        return $queryBuilder->count();
    }

    /**
     * Return all pricings in TLD format array
     *
     * @return array
     */
    public function findAllTldsAsArray()
    {
        return Pricing_Model::select('tld')->pluck('tld')->toArray();
    }

    /**
     * Get Every pricing in mod_dondominio_pricing related to tbldomainpricing and update price
     *
     * @return array
     */
    public function updateDomainPricing()
    {
        $tldSetting = $this->getApp()->getService('tld_settings');
        $whmcs = $this->getApp()->getService('whmcs');
        $pricings = $this->findPricingsInDomainPricings();

        if (count($pricings) == 0) {
            return;
        }

        foreach ($pricings as $pricing) {
            $tldSettings = $tldSetting->getTldSettingsByTld($pricing->tld);

            if (!is_null($tldSettings) && $tldSettings->ignore == 1) {
                continue;
            }

            // Update price from domain pricing

            $whmcs->savePricingsForEur($pricing);
        }

        // Update prices from domains

        $whmcs->updateDomainPrices();
    }
}