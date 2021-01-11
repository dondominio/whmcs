<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\TldSettingsService_Interface;
use WHMCS\Module\Addon\Dondominio\Models\TldSettings_Model;

class TldSettings_Service extends AbstractService implements TldSettingsService_Interface
{
    /**
     * Get TLD Settings by extension (tld)
     *
     * @param string $tld extension (tld)
     *
     * @return null|TldSettings_Model
     */
    public function getTldSettingsByTld($tld)
    {
        if (strpos($tld, ".") === false) {
            $tld = ".$tld";
        }

        return TldSettings_Model::where(['tld' => $tld])->first();
    }

    /**
     * Saves TLD Setting in DDBB
     * 
     * @param string $tld extension (tld)
     * @param array $fields Database fields
     * 
     * @return bool
     */
    public function saveTld($tld, array $fields = [])
    {
        return TldSettings_Model::updateOrInsert(['tld' => $tld], $fields);
    }
}