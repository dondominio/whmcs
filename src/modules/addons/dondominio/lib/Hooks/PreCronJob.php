<?php

namespace WHMCS\Module\Addon\Dondominio\Hooks;

use WHMCS\Module\Addon\Dondominio\App;
use Exception;

class PreCronJob
{
    public function __invoke($params)
    {
        $app = App::getInstance($params);
        $updatePices = $app->getService('settings')->getSetting('prices_autoupdate');

        // Sync with API

        /**
         * Try/catch is for legacy reasons (Is not necessary at all). 
         * If api sync fails, prices won't change therefore, prices of WHMCS won't change either.
         * At the end, this cronjob will evaluate "new prices" but as prices
         * remains the same, will do nothing other tan evaluate.
         */

        try {
            $app->getService('pricing')->apiSync(false);
            $app->getSSLService()->apiSync((bool) $updatePices);
        } catch (Exception $e) {
            // log Activity just in case
            logActivity($e);
        }

        if ($updatePices == 0) {
            return;
        }

        $app->getService('pricing')->updateDomainPricing();
    }
}