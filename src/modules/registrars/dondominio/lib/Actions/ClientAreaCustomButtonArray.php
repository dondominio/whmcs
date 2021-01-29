<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class ClientAreaCustomButtonArray extends Action
{
    public function __invoke()
    {
        return [
            "WHOIS Privacy" => "whoisPrivacy"
        ];
    }
}