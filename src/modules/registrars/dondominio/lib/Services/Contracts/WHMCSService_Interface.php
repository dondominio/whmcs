<?php

namespace WHMCS\Module\Registrar\Dondominio\Services\Contracts;

interface WHMCSService_Interface
{
    public function getConfiguration($setting);
    public function clientExistsById($id);
    public function domainExists($cname);
    public function tldExists($tld);
    public function createOrder($clientId);
    public function createDomain($orderId, $clientId, $response);
    public function findClientByEmail($email);
    public function getCustomFieldsByType($type);
    public function getCustomFieldByFieldName($fieldname);
    public function getCustomFieldsValueByEmail($fieldname, $email);
}