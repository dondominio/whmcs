<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class GetEPPCode extends Action
{
    public function __invoke()
    {
        $response = $this->app->getService('api')->getEppCode($this->domain);

        return $response->get('authcode');
    }
}