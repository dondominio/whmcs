<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface TldSettingsService_Interface
{
    public function getTldSettingsByTld($tld);
    public function saveTld($tld, array $fields = []);
}