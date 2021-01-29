<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class GetRegistrarLock extends Action
{
    public function __invoke()
    {
        $response = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

        if ($response->get("transferBlock") || ($this->params['blockAll'] == 'on' && $response->get("modifyBlock"))) {
			return "locked";
        }

        return "unlocked";
    }
}