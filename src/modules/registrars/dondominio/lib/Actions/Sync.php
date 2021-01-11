<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class Sync extends Action
{
    public function __invoke()
    {
        try {
            $response = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

            $result['active'] = in_array($response->get('status'), $this->getActiveStatus());
            $result['expired'] = in_array($response->get('status'), $this->getExpiredStatus());

            //Adding the expiry date to the information
            $expir = $response->get('tsExpir');

            if (!empty($expir)) {
                $result['expirydate'] = $expir;
            }

            //IDProtection Sync
            $protection = $this->params['idprotection'] ? true : false;

            if ($protection != $response->get("whoisPrivacy") && $result['active'] && !$result['expired']) {
                //Updating IDProtection
                $fields = ['updateType' => 'whoisPrivacy', 'whoisPrivacy' => $protection];
                $this->app->getService('api')->updateDomain($this->domain, $fields);
            }

            return $result;
        } catch (Exception $e) {
            // 2008 = Domain Not Found
            if (!$e->getCode() != 2008) {
                return [
                    'active' => false,
                    'expired' => true
                ];
            }

            throw $e;
        }
    }

    public function getActiveStatus()
    {
        return [
            'active',
            'renewed',
            'expired-renewgrace',
            'expired-redemption',
            'expired-pendingdelete'
        ];
    }

    public function getExpiredStatus()
    {
        return [
            'expired-renewgrace',
            'expired-redemption',
            'expired-pendingdelete'
        ];
    }
}