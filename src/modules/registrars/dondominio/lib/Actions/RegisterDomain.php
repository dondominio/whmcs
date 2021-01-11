<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class RegisterDomain extends Action
{
    public function __invoke()
    {
        $check = $this->app->getService('api')->checkDomain($this->domain);

        $domains = $check->get('domains');

        if (!$domains[0]['available']) {
            throw new Exception('Domain already taken');
        }

        $fields = [];

        // Nameservers
        $fields = array_merge($fields, $this->getNameserversFromParams());

		// Contact data
        $fields = array_merge($fields, $this->getContactDataFromParams());

        // TLD Requirements
        $fields = array_merge($fields, $this->getTldDataFromParams($this->params['tld'], $this->params));

        // Period
        $fields['period'] = intval($this->params['regperiod']);

        try {
            $this->app->getService('api')->createDomain($this->domain, $fields);
        } catch (Exception $e) {
            // 1100 = Insufficient Balance
            if ($e->getCode() == 1100) {
                throw new Exception('Error registering domain. Please, try again later.', $e->getCode(), $e);
            }

            throw $e;
        }
    }
}