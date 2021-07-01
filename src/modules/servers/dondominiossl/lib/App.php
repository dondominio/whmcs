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

    /**
     * Return an implementation of APIService_Interface
     *
     * @return \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
     */
    public function getApiService(): \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface
    {
        if (is_null($this->api)){
            $config = [
                'apiuser' => \WHMCS\Database\Capsule::table('mod_dondominio_settings')->where(['key' => 'api_username'])->value('value'),
                'apipasswd' => base64_decode(\WHMCS\Database\Capsule::table('mod_dondominio_settings')->where(['key' => 'api_password'])->value('value')),
            ];
            $this->api = new \WHMCS\Module\Server\Dondominiossl\Services\API_Service($config);
        }

        return $this->api;
    }

    /**
     * Process a WHMCS order for a product with the dondominiossl module
     *
     * @return string 'success' or error
     */
    public function createAccount(): string
    {
        $creator = new \WHMCS\Module\Server\Dondominiossl\Actions\CreateAccount($this->getApiService(), $this->getParams());
        return $creator->execute();
    }

    /**
     * Renew a WHMCS order for a product with the dondominiossl module
     *
     * @return string 'success' or error
     */
    public function renew(): string
    {
        $renew = new \WHMCS\Module\Server\Dondominiossl\Actions\Renew($this->getApiService(), $this->getParams());
        return $renew->execute();
    }
}

