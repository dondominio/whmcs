<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use WHMCS\Database\Capsule;

class getConfigArray extends Action
{
    public function __invoke()
    {
        // Get Custom Fields
        $customFields = $this->app->getService('whmcs')->getCustomFieldsByType('client');

        // Try to get User and Password from Addon Module
        if (Capsule::schema()->hasTable('mod_dondominio_settings')) {
            $api_username = Capsule::table('mod_dondominio_settings')->where('key', 'api_username')->value('value');
            $api_password = Capsule::table('mod_dondominio_settings')->where('key', 'api_password')->value('value');
        } else {
            $api_username = "";
            $api_password = "";
        }

        if (!empty($api_password)) {
            $api_password = base64_decode($api_password);
        }

        return [
            "FriendlyName" => [
                "Type" => "System",
                "Value" => "DonDominio"
            ],
            "Description" => [
                "Type" => "System",
                "Value" => "Register domains with DonDominio! Signup at <a href='https://www.dondominio.com/register/'>https://www.dondominio.com/register/</a></strong>"
            ],
            //API login details
            "apiuser" => [
                "FriendlyName" => "API Username",
                "Type" => "text",
                "Size" => "25",
                "Description" => "Enter your API Username here",
                "Default" => $api_username
            ],
            "apipasswd" => [
                "FriendlyName" => "API Password",
                "Type" => "password",
                "Size" => "25",
                "Description" => "Enter your API Password here",
                "Default" => $api_password
            ],
            //VAT Number custom field
            "vat" => [
                "FriendlyName" => "VAT Number Field",
                "Type" => "dropdown",
                "Options" => "," . implode(",", $customFields),
                "Description" => "Custom field containing the VAT Number for your customers"
            ],
            //FOACONTACT	
            "foacontact" => [	
                "FriendlyName" => "FOA Contact",	
                "Type" => "dropdown",	
                "Options" => "Owner,Admin",	
                "Description" => "Domain contact to use for FOA"	
            ],
            //Owner Contact Override
            "ownerContact" => [
                "FriendlyName" => "Owner Contact DonDominio ID",
                "Type" => "text",
                "Size" => "20",
                "Description" => "Override Owner contact information provided by customer"
            ],
            "allowOwnerContactUpdate" => [
                "FriendlyName" => " ",
                "Type" => "yesno",
                "Description" => "Allow customers to modify Owner contact information"
            ],
            //Admin Contact Override
            "adminContact" => [
                "FriendlyName" => "Admin Contact DonDominio ID",
                "Type" => "text",
                "Size" => "20",
                "Description" => "Override Admin contact information provided by customer"
            ],
            "allowAdminContactUpdate" => [
                "FriendlyName" => " ",
                "Type" => "yesno",
                "Description" => "Allow customers to modify Admin contact information"
            ],
            //Tech Contact Override
            "techContact" => [
                "FriendlyName" => "Tech Contact DonDominio ID",
                "Type" => "text",
                "Size" => "20",
                "Description" => "Override Tech contact information provided by customer"
            ],
            "allowTechContactUpdate" => [
                "FriendlyName" => " ",
                "Type" => "yesno",
                "Description" => "Allow customers to modify Tech contact information"
            ],
            //Billing Contact Override
            "billingContact" => [
                "FriendlyName" => "Billing Contact DonDominio ID",
                "Type" => "text",
                "Size" => "20",
                "Description" => "Override Billing contact information provided by customer"
            ],
            "allowBillingContactUpdate" => [
                "FriendlyName" => " ",
                "Type" => "yesno",
                "Description" => "Allow customers to modify Tech contact information"
            ],
            //Double-block
            "blockAll" => [
                "FriendlyName" => "Lock modifications",
                "Type" => "yesno",
                "Description" => "Locking domain transfers also locks domain updates"
            ]
        ];
    }
}