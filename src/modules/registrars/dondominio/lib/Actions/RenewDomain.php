<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class RenewDomain extends Action
{
    public function __invoke()
    {
        $response = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

        $fields = [
            'curExpDate' => $response->get('tsExpir'),
            'period' => (int) $this->params["regperiod"]
        ];

        try {
            $this->app->getService('api')->renewDomain($this->domain, $fields);
        } catch (Exception $e) {
            // 1100 = Insufficient Balance
            if ($e->getCode() == 1100) {
                throw new Exception('Error renewing domain. Please, try again later.', $e->getCode(), $e);
            }

            throw $e;
        }
    }
}