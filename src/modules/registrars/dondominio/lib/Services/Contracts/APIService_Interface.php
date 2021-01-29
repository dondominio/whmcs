<?php

namespace WHMCS\Module\Registrar\Dondominio\Services\Contracts;

interface APIService_Interface
{
    public function getDomainList();
    public function getDomainInfo($domain, $infoType);
    public function checkDomain($domain);
    public function createDomain($domain, array $params);
    public function transferDomain($domain, array $params);
    public function renewDomain($domain, array $params);
    public function updateDomain($domain, array $params);
    public function getNameservers($domain);
    public function updateNameservers($domain, array $params);
    public function updateContactDetails($domain, array $params);
    public function getEppCode($domain);
    public function createGlueRecord($domain, array $params);
    public function updateGlueRecord($domain, array $params);
    public function deleteGlueRecord($domain, array $params);
}