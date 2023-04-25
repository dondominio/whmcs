<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class GetContactDetails extends Action
{
    public function __invoke()
    {
        $response = $this->app->getService('api')->getDomainInfo($this->domain, 'contact');

        $result = [];

        if (empty($this->params['ownerContact']) || $this->params['allowOwnerContactUpdate'] === 'on') {
            $result['Registrant'] = static::filterContactFromResponse($response->get('contactOwner'));
        }

        if (empty($this->params['adminContact']) || $this->params['allowAdminContactUpdate'] === 'on') {
            $result['Admin'] = static::filterContactFromResponse($response->get('contactAdmin'));
        }

        if (empty($this->params['techContact']) || $this->params['allowTechContactUpdate'] === 'on') {
            $result['Tech'] = static::filterContactFromResponse($response->get('contactTech'));
        }

        if (empty($this->params['billingContact']) || $this->params['allowBillingContactUpdate'] === 'on') {
            $result['Billing'] = static::filterContactFromResponse($response->get('contactBilling'));
        }

        if (count($result) == 0) {
            throw new Exception('Contact modification is disabled. Contact support for more information.');
        }

        return $result;
    }

    protected static function filterContactFromResponse($response)
    {
        return [
            'First Name' => $response['firstName'],
            'Last Name' => $response['lastName'],
            'Company Name' => $response['orgName'],
            'Email Address' => $response['email'],
            'Address' => $response['address'],
            'City' => $response['city'],
            'State' => $response['state'],
            'Zip Code' => $response['postalCode'],
            'Country' => $response['country'],
            'Phone Number' => $response['phone'],
            'VAT Number' => $response['identNumber']
        ];
    }
}
