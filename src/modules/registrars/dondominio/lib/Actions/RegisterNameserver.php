<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class RegisterNameserver extends Action
{
    public function __invoke()
    {
        $fields = [
            'name' => $this->params['nameserver'],
            'ipv4' => $this->params['ipaddress']
        ];

        $this->app->getService('api')->createGlueRecord($this->domain, $fields);
    }
}