<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;
use WHMCS\Domain\Domain;

class Sync extends Action
{
    private const NOT_FOUND_DOMAIN_API_CODE = 2008;

    /**
     * IMPORTANT!: When this function is modified, synchronize with the syncDomain function in WHMCS\Module\Addon\Dondominio\Services\WHMCS_Service.
     */
    public function __invoke()
    {
        try {
            $response = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

            $result['active'] = in_array($response->get('status'), $this->getActiveStatus());
            $result['expired'] = in_array($response->get('status'), $this->getExpiredStatus());

            // Adding the expiry date to the information
            $expir = $response->get('tsExpir');

            if (!empty($expir)) {
                $result['expirydate'] = $expir;
            }

            // IDProtection Sync
            $protection = $this->params['idprotection'] ? true : false;

            if ($protection != $response->get('whoisPrivacy') && $result['active'] && !$result['expired']) {
                // Updating IDProtection
                $fields = ['updateType' => 'whoisPrivacy', 'whoisPrivacy' => $protection];
                $this->app->getService('api')->updateDomain($this->domain, $fields);
            }

            return $result;
        } catch (Exception $e) {
            if ((int) $e->getCode() === static::NOT_FOUND_DOMAIN_API_CODE) {
                $this->calcNotFound($result);

                return $result;
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
            'expired-pendingdelete',
        ];
    }

    public function getExpiredStatus()
    {
        return [
            'expired-renewgrace',
            'expired-redemption',
            'expired-pendingdelete',
        ];
    }

    private function calcNotFound(&$result)
    {
        $domain = Domain::find($this->getParam('domainid'));

        if (is_null($domain)) {
            return;
        }

        $isExpired = $domain->expirydate->isPast();
        $actualStatus = $domain->status;
        $previousTransferredAwayStatus = [
            'Active',
            'Transferred Away',
        ];

        if (!$isExpired && in_array($actualStatus, $previousTransferredAwayStatus)) {
            $result['active'] = false;
            $result['transferredAway'] = true;
        }
    }
}
