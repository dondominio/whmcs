<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class CreateAccount
{
    protected array $params = [];
    protected \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api;

    public function __construct(
        \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api,
        array $params
    ) {
        $this->api = $api;
        $this->params = $params;
    }

    public function execute(): array
    {  
        $arg = [];

        try {
            $this->api->createCertificate($arg);
        } catch (\Exception $e){
            return $e->getMessage();
        }

        return [];
    }
}
