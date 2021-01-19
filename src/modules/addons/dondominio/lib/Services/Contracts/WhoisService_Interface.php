<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface WhoisService_Interface
{
    public function getCustomWhoisServerFilePath();
    public function getWhoisItems();
    public function setup($new_tld);
    public function importWhois(array $file);
    public function doWhois($domain);
}