<?php

namespace WHMCS\Module\Addon\Dondominio\Hooks;

use WHMCS\Module\Addon\Dondominio\App;
use Exception;

class PreCronJob
{
    public function __invoke($params)
    {
        $app = App::getInstance($params);

        // Sync with API

        /**
         * Try/catch is for legacy reasons (Is not necessary at all). 
         * If api sync fails, prices won't change therefore, prices of WHMCS won't change either.
         * At the end, this cronjob will evaluate "new prices" but as prices
         * remains the same, will do nothing other tan evaluate.
         */

        try {
            $app->getService('pricing')->apiSync(false);
        } catch (Exception $e) {
            // log Activity just in case
            logActivity($e);
        }

        if ($app->getService('settings')->getSetting('prices_autoupdate') == 0) {
            return;
        }

        // Get Every pricing in mod_dondominio_pricing related to tbldomainpricing
        // and update price

        $pricings = $app->getService('pricing')->findPricingsInDomainPricings();

        if (count($pricings) == 0) {
            return;
        }

        foreach ($pricings as $pricing) {
            $tldSettings = $app->getService('tld_settings')->getTldSettingsByTld($pricing->tld);

            if (!is_null($tldSettings) && $tldSettings->ignore == 1) {
                continue;
            }

            // Update price from domain pricing

            $app->getService('whmcs')->savePricingsForEur($pricing);
        }

        // Update prices from domains

        $app->getService('whmcs')->updateDomainPrices();
    }
}