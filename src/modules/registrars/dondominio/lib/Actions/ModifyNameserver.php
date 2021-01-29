<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class ModifyNameserver extends Action
{
    public function __invoke()
    {
        $fields = [
            'name' => $this->params['nameserver'],
            'ipv4' => $this->params['newipaddress']
        ];

        $this->app->getService('api')->updateGlueRecord($this->domain, $fields);
    }
}