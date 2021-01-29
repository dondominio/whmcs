<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class SaveContactDetails extends Action
{
    public function __invoke()
    {
        $fields = [];

        // Filter owner contact
        if (substr($this->params['tld'], -2, 2) != 'es') {
            $fields = array_merge($fields, $this->getContactTypeDataFromParams('Registrant', 'owner'));
        }

        // Filter admin contact
        $fields = array_merge($fields, $this->getContactTypeDataFromParams('Admin', 'admin'));

        // Filter tech contact
        $fields = array_merge($fields, $this->getContactTypeDataFromParams('Tech', 'tech'));

        $response = $this->app->getService('api')->updateContactDetails($this->domain, $fields);

        if (count($response) == 0) {
            throw new Exception('Contact modification is disabled. Contact support for more information.');
        }
    }

    protected function getContactTypeDataFromParams($key, $type)
    {
        $fields = [];

        $contact = $this->params['contactdetails'][$key];

        $allowUpdate = empty($this->params[$type. 'Contact']) || $this->params['allow'.ucfirst($type).'ContactUpdate'] == 'on';

        if ($allowUpdate && !empty($contact['First Name'])) {
            $fields[$type.'ContactType'] = 'individual';
            $fields[$type.'ContactFirstName'] = $contact['First Name'];
            $fields[$type.'ContactLastName'] = $contact['Last Name'];
            $fields[$type.'ContactOrgName'] = $contact['Company Name'];
            $fields[$type.'ContactEmail'] = $contact['Email Address'];
            $fields[$type.'ContactAddress'] = $contact['Address'];
            $fields[$type.'ContactCity'] = $contact['City'];
            $fields[$type.'ContactState'] = $contact['State'];
            $fields[$type.'ContactCountry'] = $contact['Country'];
            $fields[$type.'ContactPhone'] = $contact['Phone Number'];

            // zip code

            $pcode = "";
	
            if (array_key_exists('Zip Code', $contact)) {
                $pcode = $contact['Zip Code'];
             } else if (array_key_exists('Postcode', $contact)) {
                $pcode = $contact['Postcode'];
            } else if (array_key_exists('ZIP Code', $contact)) {
                $pcode = $contact['ZIP Code'];
            }

            $fields[$type.'ContactPostalCode'] = $pcode;

            // vat number

            $contactIdentNumber = $contact['VAT Number'] ?:
                $this->app->getService('whmcs')->getCustomFieldsValueByEmail($this->params['vat'], $contact['Email Address']);

            $fields[$type.'ContactIdentNumber'] = $contactIdentNumber;

            // check if organization for spanish contacts case

            if($contactIdentNumber && $contact['Country'] == 'ES'){
                if (preg_match('/^[a-zA-Z]+[0-9]+$/', $contactIdentNumber)) {
                    $fields[$type.'ContactType'] = 'organization';
                    $fields[$type.'ContactOrgType'] = static::mapOrgType($contactIdentNumber);
                }
            }
        } else if (!empty($this->params[$type.'Contact'])) {
            $fields[$type.'ContactID'] = $this->params[$type.'Contact'];
        }

        return $fields;
    }
}