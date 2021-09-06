<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use WHMCS\Module\Registrar\Dondominio\App;

class Action
{
    protected $app;
    protected $params;
    protected $domain;

    public function __construct(App $app, array $params)
    {
        $this->app = $app;
        $this->params = $params;

        if (array_key_exists('original', $this->params)) {
            $params['sld'] = $this->params['original']['sld'];
            $params['tld'] = $this->params['original']['tld'];
        }

        $this->domain = implode(".", [$this->params["sld"], $this->params["tld"]]);
    }

    public function getApp()
    {
        return $this->app;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($key)
    {
        if (!array_key_exists($key, $this->params)) {
            return null;
        }

        return $this->params[$key];
    }

    public function getDomain()
    {
        return $this->domain;
    }

    protected function getNameserversFromParams()
    {
        $nameservers = ['ns1', 'ns2', 'ns3', 'ns4', 'ns5'];
        $nameservers_array = [];

        foreach ($nameservers as $ns) {
            if (array_key_exists($ns, $this->params)) {
                $nameservers_array[] = $this->params[$ns];
            }
        }

        return [
			'nameservers' => implode(',', $nameservers_array),
        ];
    }

    protected function getTldDataFromParams()
    {
        $fields = [];

        switch ($this->params['tld']) {
            case 'aero':
                $fields['aeroId'] = $this->params['additionalfields']['ID'];
                $fields['aeroPass'] = $this->params['additionalfields']['Password'];
                break;
            case 'cat':
            case 'pl':
            case 'scot':
            case 'eus':
            case 'gal':
            case 'quebec':
                $fields['domainIntendedUse'] = $this->params['additionalfields']['Intended Use'];
                break;
            case 'fr':
                $fields['ownerDateOfBirth'] = $this->params['additionalfields']['Birthdate'];
                $fields['frTradeMark'] = $this->params['additionalfields'][''];
                $fields['frSirenNumber'] = $this->params['additionalfields'][''];
                break;
            case 'hk':
                $fields['ownerDateOfBirth'] = $this->params['additionalfields']['Birthdate'];
                break;
            case 'jobs':
                $fields['jobsOwnerWebsite'] = $this->params['additionalfields']['Owner Website'];
                $fields['jobsAdminWebsite'] = $this->params['additionalfields']['Admin Contact Website'];
                $fields['jobsContactWebsite'] = $this->params['additionalfields']['Tech Contact Website'];
                $fields['jobsBillingWebsite'] = $this->params['additionalfields']['Billing Contact Website'];
                break;
            case 'lawyer':
            case 'attorney':
            case 'dentist':
            case 'airforce':
            case 'army':
            case 'navy':
                $fields['coreContactInfo'] = $this->params['additionalfields']['Contact Info'];
                break;
            case 'ltda':
                $fields['ltdaAuthority'] = $this->params['additionalfields']['Authority'];
                $fields['ltdaLicenseNumber'] = $this->params['additionalfields']['License Number'];
                break;
            case 'pro':
                $fields['proProfession'] = $this->params['additionalfields']['Profession'];
                break;
            case 'ru':
                $fields['ownerDateOfBirth'] = $this->params['additionalfields']['Birthdate'];
                $fields['ruIssuer'] = $this->params['additionalfields']['Issuer'];
                $fields['ruIssuerDate'] = $this->params['additionalfields']['Issue Date'];
                break;
            case 'travel':
                $fields['travelUIN'] = $this->params['additionalfields']['UIN'];
                break;
            case 'xxx':
                $fields['xxxClass'] = $this->params['additionalfields']['Class'];
                $fields['xxxName'] = $this->params['additionalfields']['Name'];
                $fields['xxxEmail'] = $this->params['additionalfields']['Email'];
                $fields['xxxId'] = $this->params['additionalfields']['Member Id'];
                break;
            case 'law':
            case 'abogado':
                $fields['lawaccid'] = $this->params['additionalfields']['Accreditation ID'];
                $fields['lawaccbody'] = $this->params['additionalfields']['Accreditation Body'];
                $fields['lawaccyear'] = $this->params['additionalfields']['Accreditation Year'];
                $fields['lawaccjurcc'] = $this->params['additionalfields']['Country'];
                $fields['lawaccjurst'] = $this->params['additionalfields']['State/Province'];
                break;
        }
        
        return $fields;
    }

    protected function getContactDataFromParams()
    {
        $fields = [];

        $adminContactIdentNumber = $this->getVATNumber();

        $nif_letra = strtoupper(substr($adminContactIdentNumber, 0, 1));

        if ($nif_letra != 'X' && $nif_letra != 'Y' && $nif_letra != 'Z' && !is_numeric($nif_letra)) {
            $ownerContactType = 'organization';
        } else {
            $ownerContactType = 'individual';
        }

        // Owner Contact

        if (empty($this->params['ownerContact'])) {
            $ownerContactFields = [
                'ownerContactType' => $ownerContactType,
                'ownerContactFirstName' => $this->params['firstname'],
                'ownerContactLastName' => $this->params['lastname'],
                'ownerContactIdentNumber' => $adminContactIdentNumber,
                'ownerContactOrgName' => $this->params['companyname'],
                'ownerContactOrgType' => static::mapOrgType($adminContactIdentNumber),
                'ownerContactEmail' => $this->params['email'],
                'ownerContactPhone' => $this->params['fullphonenumber'],
                'ownerContactAddress' => $this->params['address1'] . "\r\n" . $this->params['address2'],
                'ownerContactPostalCode' => $this->params['postcode'],
                'ownerContactCity' => $this->params['city'],
                'ownerContactState' => $this->params['state'],
                'ownerContactCountry' => $this->params['country']
            ];
        } else {
            $ownerContactFields = ['ownerContactID' => $this->params['ownerContact']];
        }

        $fields = array_merge($fields, $ownerContactFields);

        // Admin Contact

        if (empty($this->params['adminContact'])) {
            if (array_key_exists('Administrative Document Number', $this->params['additionalfields'])) {
                $adminContactType = 'individual';
                $adminContactIdentNumber = $this->params['additionalfields']['Administrative Document Number'];
                $adminContactOrgType = '';
            } else {
                $adminContactType = $ownerContactType;
                $adminContactOrgType = static::mapOrgType($adminContactIdentNumber);
            }

            $adminContactFields = [
                'adminContactType' => $adminContactType,
                'adminContactFirstName' => $this->params['adminfirstname'],
                'adminContactLastName' => $this->params['adminlastname'],
                'adminContactIdentNumber' => $adminContactIdentNumber, 
                'adminContactOrgName' => $this->params['companyname'],
                'adminContactOrgType' => $adminContactOrgType,
                'adminContactEmail' => $this->params['adminemail'],
                'adminContactPhone' => $this->params['adminfullphonenumber'],
                'adminContactAddress' => $this->params['adminaddress1'] . "\r\n" . $this->params['adminaddress2'],
                'adminContactPostalCode' => $this->params['adminpostcode'],
                'adminContactCity' => $this->params['admincity'],
                'adminContactState' => $this->params['adminstate'],
                'adminContactCountry' => $this->params['admincountry']
            ];
        }else{
            $adminContactFields = ['adminContactID' => $this->params['adminContact']];
        }

        $fields = array_merge($fields, $adminContactFields);

        // Tech Contact

        if (!empty($this->params['techContact'])) {
            $fields['techContactID'] = $this->params['techContact'];
        }

        // Billing Contact

        if (!empty($this->params['billingContact'])) {
            $fields['billingContactID'] = $this->params['billingContact'];
        }

        return $fields;
    }

    /**
     * Map VAT number to the corresponding additional domain field.
     * Returns the VAT number from the additional parameters passed by
     * the register domain call made in WHMCS.
     * @param string $tld TLD
     * @param array $params Parameters passed by WHMCS
     * @return string
     */
    protected function getVATNumber()
    {
        /**
         * WHMCS 7.1 new "ID Form Number" field override.
         */
        if(array_key_exists('ID Form Number', $this->params['additionalfields']) && strlen($this->params['additionalfields']['ID Form Number']) > 0) {
            return $this->params['additionalfields']['ID Form Number'];
        }

        /**
         * VAT Number custom field override.
         */
        if (array_key_exists('VAT Number', $this->params['additionalfields']) && strlen($this->params['additionalfields']['VAT Number'] ) > 0) {
            return $this->params['additionalfields']['VAT Number'];
        }

        $vatNumber = '';

        /**
         * Vat Number by TLD
         */
        if (substr($this->params['tld'], 0, 1) == '.') {
            $tld = substr($this->params['tld'], 1);
        }

        $tldMap = [
            $this->params['additionalfields']['Company ID Number'] => ['co.uk', 'net.uk', 'org.uk', 'me.uk', 'plc.uk', 'ltd.uk', 'uk'],
            $this->params['additionalfields']['ID Form Number'] => ['es'],
            $this->params['additionalfields']['RCB Singapore ID'] => ['sg', 'com.sg', 'edu.sg', 'net.sg', 'org.sg', 'per.sg'],
            $this->params['additionalfields']['Tax ID'] => ['it', 'de'],
            $this->params['additionalfields']['Registrant ID'] => ['com.au', 'net.au', 'org.au', 'asn.aun', 'id.au'],
            $this->params['additionalfields']['Identity Number'] => ['asia']
        ];

        foreach ($tldMap as $key => $arr) {
            if (in_array($tld, $arr)) {
                $vatNumber = $key;
                break;
            }
        }

        // Search by Custom Field

        if (empty($vatNumber) && !empty($this->params['vat'])) {
            $vatNumberCustomField = $this->app->getService('whmcs')->getCustomFieldByFieldName($this->params['vat']);

            // Try to find custom field through old select version
            // Compatibility with 2.0.x and 2.1.x versions
            if (is_null($vatNumberCustomField)) {
                $customFields = $this->app->getService('whmcs')->getCustomFieldsByType('client');
                $select = array_merge([""], $customFields);

                if (array_key_exists($this->params['vat'], $select)) {
                    $vatNumberCustomField = $this->app->getService('whmcs')->getCustomFieldByFieldName($select[$this->params['vat']]);
                }
            }

            if (is_object($vatNumberCustomField)) {
                foreach ($this->params['customfields'] as $customField) {
                    if ($customField['id'] == $vatNumberCustomField->id) {
                        $vatNumber = $customField['value'];
                        break;
                    }
                }

                if (empty($vatNumber)) {
                    $vatNumber = $this->params['customfields' . ($vatNumberCustomField->id - 1)];
                }
            }
        }

        return $vatNumber;
    }

    /**
     * Convert organization type to the corresponding code for the API using a VAT Number.
     * @param string $vat VAT Number used to get the code
     * @return string
     */
    protected static function mapOrgType($vat)
    {
        $letter = substr($vat, 0, 1);
        
        if (is_numeric($letter)) {
            return "1";
        }

        $map = [
            "524" => ["A"],
            "612" => ["B"],
            "560" => ["C"],
            "562" => ["D"],
            "150" => ["E"],
            "566" => ["F"],
            "47" => ["G"],
            "554" => ["J"],
            "747" => ["P"],
            "746" => ["Q"],
            "164" => ["R"],
            "436" => ["S"],
            "717" => ["U"],
            "877" => ["V"],
            "713" => ["N", "W"],
            "1" => ["X", "Y", "Z"]
        ];

        foreach ($map as $key => $arr) {
            if (in_array($letter, $arr)) {
                return $key;
            }
        }

        return "877";
    }
}
