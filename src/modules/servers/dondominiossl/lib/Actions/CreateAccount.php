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

    public function execute(): string
    {
        if (empty($this->params['customfields']['CSR'])){
            return 'CSR Data not found';
        }

        if (empty($this->params['customfields']['Admin Contact ID'])){
            return 'Admin Contact ID not found';
        }

        if (empty($this->params['configoption3'])){
            return 'Product ID not found';
        }

        $args = [
            'productID' => $this->params['configoption3'],
            'csrData' => $this->params['customfields']['CSR'],
            'adminContactID' => $this->params['customfields']['Admin Contact ID'],
        ];

        try {
            $this->api->createCertificate($args);
        } catch (\Exception $e){
            return $e->getMessage();
        }

        return 'success';
    }
}
