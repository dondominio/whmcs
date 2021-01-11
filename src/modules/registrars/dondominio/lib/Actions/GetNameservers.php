<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class GetNameservers extends Action
{
    public function __invoke()
    {
        $response = $this->app->getService('api')->getNameServers($this->domain);

        $result = [];

        foreach ($response->get("nameservers") as $key => $nameserver) {
            if ($key <= 5) {
                $result["ns" . $nameserver["order"]] = $nameserver["name"];
            }
        }

        return $result;
    }
}