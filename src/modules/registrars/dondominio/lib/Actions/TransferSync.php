<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class TransferSync extends Action
{
    public function __invoke()
    {
        $response = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

        $status = $response->get('status');

        $result = [
            'completed' => false,
            'failed' => false
        ];

        if ($status == 'active' || $status == 'renewed') {
            $result['completed'] = true;
            $result['expirydate'] = $response->get('tsExpir');
        } else if ($status == 'transfer-cancel') {
            $result['failed'] = true;
        }

        return $result;
    }
}