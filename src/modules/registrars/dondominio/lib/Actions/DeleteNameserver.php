<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class DeleteNameserver extends Action
{
    public function __invoke()
    {
        $fields = [
            'name' => $this->params['nameserver']
        ];

        $this->app->getService('api')->deleteGlueRecord($this->domain, $fields);
    }
}