<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

use \stdClass;

interface APIService_Interface
{
    public function reload(array $apiOptions = []);
    public function getDomainInfo($domain);
    public function getDomainList($page = null, $pageLength = null);
    public function updateContact($domain, $type, $ddid);
    public function transferDomain(stdClass $extDomain, $authCode, array $clientDetails);
    public function checkDomain($domain);
    public function getDomainSuggestions($text);
    public function getAccountZones($params);
}