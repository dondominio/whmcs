<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class SaveNameservers extends Action
{
    public function __invoke()
    {
        $fields = [];

        // Nameservers
        $fields = array_merge($fields, $this->getNameserversFromParams());
        $nameservers = explode(',', $fields['nameservers']);

        $this->app->getService('api')->updateNameservers($this->domain, $nameservers);
    }
}