<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class IDProtectToggle extends Action
{
    public function __invoke()
    {
        $block = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

        if (array_key_exists('modifyBlock', $block->getResponseData()) && $block->get('modifyBlock')) {
            throw new Exception('Domain has the modification lock enabled. Unlock modifications to proceed.');
        }

        $this->app->getService('api')->updateDomain($this->domain, [
            'updateType' => 'whoisPrivacy',
            'whoisPrivacy' => $this->params['protectenable'] ? true : false
        ]);
    }
}