<?php

namespace WHMCS\Module\Server\Dondominiossl;


class App
{
    protected array $params = [];
    protected ?\WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api = null;


    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getApiService(): \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
    {
        if (is_null($this->api)){
            $config = [
                'apiuser' => \WHMCS\Database\Capsule::table('mod_dondominio_settings')->where(['key' => 'api_username'])->value('value'),
                'apipasswd' => \WHMCS\Database\Capsule::table('mod_dondominio_settings')->where(['key' => 'api_password'])->value('value'),
            ];
            $this->api = new \WHMCS\Module\Server\Dondominiossl\Services\API_Service($config);
        }

        return $this->api;
    }

    public function createAccount(): string
    {
        $creator = new \WHMCS\Module\Server\Dondominiossl\Actions\CreateAccount($this->getApiService(), $this->getParams());
        return $creator->execute();
    }

}

