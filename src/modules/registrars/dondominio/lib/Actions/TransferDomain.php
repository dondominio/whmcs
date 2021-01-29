<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class TransferDomain extends Action
{
    public function __invoke()
    {
        $fields = [];

        // Nameservers
        $fields = array_merge($fields, $this->getNameserversFromParams());

		// Contact data
        $fields = array_merge($fields, $this->getContactDataFromParams());

        // TLD Requirements
        $fields = array_merge($fields, $this->getTldDataFromParams());

        // Auth Code
        $fields['authcode'] = $this->params['transfersecret'];

        // FOA Contact	
        if (!empty($this->params['foacontact'])) {	
            $fields['foacontact'] = strtolower($this->params['foacontact']);	
        }

        try {
            $this->app->getService('api')->transferDomain($this->domain, $fields);
        } catch (Exception $e) {
            // 1100 = Insufficient Balance
            if ($e->getCode() == 1100) {
                throw new Exception('Error transfering domain. Please, try again later.', $e->getCode(), $e);
            }

            throw $e;
        }
    }
}