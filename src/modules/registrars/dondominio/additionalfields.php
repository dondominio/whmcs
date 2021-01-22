<?php

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * !! WARNING																 !!
 * !! YOU SHOULD NOT MODIFY THIS FILE UNDER ANY CIRCUMSTANCES, UNLESS		 !!
 * !! INSTRUCTED SO BY THE DONDOMINIO/MRDOMAIN SUPPORT TEAM.				 !!
 * !!																		 !!
 * !! Making any changes to this file may cause the DonDominio WHMCS Module  !!
 * !! to stop working or register invalid domains/information.				 !!
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * Automatically adding fields needed to register domains with DonDominio/MrDomain.
 *
 * Please include this file in /path/to/whmcs/includes/additionaldomainfields.php:
 *
 * <?php
 *
 * //...file contents
 *
 * include('../modules/registrars/dondominio/additionaldomainfields.php');
 *
 * ?>
 *
 * API version 0.9.x
 * WHMCS version 5.2.x / 5.3.x
 * @link https://github.com/dondominio/dondominiowhmcs
 * @package DonDominioWHMCS
 * @license CC BY-ND 3.0 <http://creativecommons.org/licenses/by-nd/3.0/>
 */

use WHMCS\Database\Capsule;

$extensionsMap = [
    'Company ID Number' => ['.uk', '.co.uk', '.net.uk', '.org.uk', '.me.uk', '.plc.uk', '.tld.uk'],
    'ID Form Number' => ['.es'],
    'RCB Singapore ID' => ['.sg', '.com.sg', '.edu.sg', '.net.sg', '.org.sg', '.per.sg'],
    'Tax ID' => ['.it', '.de'],
    'Registrant ID' => ['.com.au', '.net.au', '.org.au', '.asn.au', '.id.au'],
    'Identity Number' => ['.asia'],
    'VAT Number' => ['.fr']
];

$domainPricings = Capsule::table('tbldomainpricing')->where('autoreg', 'dondominio')->get();

foreach ($domainPricings as $domainPricing) {
    $add = true;

    // Search in extensionsMap
    foreach ($extensionsMap as $field => $extensions) {
        if (!in_array($domainPricing->extension, $extensions)) {
            continue;
        }

        // Extension found in extensionsMap -> $field
        // Searching for already defined fields
        foreach ($additionaldomainfields[$domainPricing->extension] as $additionaldomainfield) {
            if ($additionaldomainfield['Name'] == $field) {
                $add = false;
                break;
            }
        }
    }

    // Adding any missing "VAT Number" fields to the Additional Domain Fields
    if ($add) {
        $additionaldomainfields[$domainPricing->extension][] = [
            'Name' => 'VAT Number',
            'LangVar' => 'vat',
            'Type' => 'text',
            'Size' => 50,
            'Default' => '',
            'Required' => true
        ];
    }
}

/*
 * Other fields.
 */
$additionaldomainfields['.es'][] = [
    'Name' => 'Administrative Document Number',
    'LangVar' => 'es_admin_vat',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
 
if (array_key_exists(".es", $additionaldomainfields)) {
    $additionaldomainfields[".com.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".org.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".nom.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".gob.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".edu.es"] = $additionaldomainfields['.es'];
}

// .AERO
$additionaldomainfields['.aero'][] = [
    'Name' => 'ID',
    'LangVar' => 'aero_id',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.aero'][] = [
    'Name' => 'Password',
    'LangVar' => 'aero_pass',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];

// .BARCELONA, .CAT, .PL, .SCOT, .EUS, .GAL, .QUEBEC
$additionaldomainfields['.barcelona'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'barcelona_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.cat'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'cat_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.pl'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'pl_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.scot'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'scot_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.eus'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'eus_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.gal'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'gal_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.quebec'][] = [
    'Name' => 'Intended Use',
    'LangVar' => 'quebec_intendeduse',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];

// .HK
$additionaldomainfields['.hk'][] = [
    'Name' => 'Birthdate',
    'LangVar' => 'hk_birthdate',
    'Type' => 'text',
    'Size' => 16,
    'Default' => '1900-01-01',
    'Required' => true
];

// .JOBS
$additionaldomainfields['.jobs'][] = [
    'Name' => 'Owner Website',
    'LangVar' => 'jobs_ownerwebsite',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.jobs'][] = [
    'Name' => 'Admin Contact Website',
    'LangVar' => 'jobs_adminwebsite',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.jobs'][] = [
    'Name' => 'Tech Contact Website',
    'LangVar' => 'jobs_techwebsite',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.jobs'][] = [
    'Name' => 'Billing Contact Website',
    'LangVar' => 'jobs_billingwebsite',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];

// .LAWYER, .ATTORNEY, .DENTIST, .AIRFORCE, .ARMY, .NAVY
$contactInfoField = [
    'Name' => 'Contact Info',
    'LangVar' => 'lawyer_contactinfo',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.lawyer'][] = $contactInfoField; 
$additionaldomainfields['.attorney'][] = $contactInfoField;	
$additionaldomainfields['.dentist'][] = $contactInfoField;
$additionaldomainfields['.airforce'][] = $contactInfoField;
$additionaldomainfields['.army'][] = $contactInfoField;
$additionaldomainfields['.navy'][] = $contactInfoField;

// .LTDA
$additionaldomainfields['.ltda'][] = [
    'Name' => 'Authority',
    'LangVar' => 'ltda_authority',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => false
];
$additionaldomainfields['.ltda'][] = [
    'Name' => 'License Number',
    'LangVar' => 'ltda_license',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => false
];

// .RU
$additionaldomainfields['.ru'][] = [
    'Name' => 'Birthdate',
    'LangVar' => 'ru_birthdate',
    'Type' => 'text',
    'Size' => 16,
    'Default' => '1900-01-01',
    'Required' => false
];
$additionaldomainfields['.ru'][] = [
    'Name' => 'Issuer',
    'LangVar' => 'ru_issuer',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => false
];
$additionaldomainfields['.ru'][] = [
    'Name' => 'Issue Date',
    'LangVar' => 'ru_issuedate',
    'Type' => 'text',
    'Size' => 16,
    'Default' => '1900-01-01',
    'Required' => false
];

// .TRAVEL
$additionaldomainfields['.travel'][] = [
    'Name' => 'UIN',
    'LangVar' => 'travel_uin',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];

// .XXX
$additionaldomainfields['.xxx'][] = [
    'Name' => 'Class',
    'LangVar' => 'xxx_class',
    'Type' => 'dropdown',
    'Options'=>'default|Non-Member of .XXX,membership|Member of .XXX,nonResolver|Do not resolve DNS',
    'Default' => 'default|Default'
];
$additionaldomainfields['.xxx'][]= [
    'Name' => 'Name',
    'LangVar' => 'xxx_name',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => false
];
$additionaldomainfields['.xxx'][] = [
    'Name' => 'Email',
    'LangVar' => 'xxx_email',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => false
];
$additionaldomainfields['.xxx'][] = [
    'Name' => 'Member Id',
    'LangVar' => 'xxx_memberid',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => false
];

// .LAW, .ABOGADO
$additionaldomainfields['.law'][] = [
    'Name' => 'Accreditation ID',
    'LangVar' => 'law_accid',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name' => 'Accreditation Body',
    'LangVar' => 'law_accbody',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name' => 'Accreditation Year',
    'LangVar' => 'law_accyear',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name' => 'Country',
    'LangVar' => 'law_acccountry',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name' => 'State/Province',
    'LangVar' => 'law_accprovince',
    'Type' => 'text',
    'Size' => 50,
    'Default' => '',
    'Required' => true
];
$additionaldomainfields['.abogado'] = $additionaldomainfields['.law'];