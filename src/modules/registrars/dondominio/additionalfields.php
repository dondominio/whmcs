<?php

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * !! WARNING                                                                 !!
 * !! YOU SHOULD NOT MODIFY THIS FILE UNDER ANY CIRCUMSTANCES, UNLESS         !!
 * !! INSTRUCTED SO BY THE DONDOMINIO/MRDOMAIN SUPPORT TEAM.                 !!
 * !!                                                                         !!
 * !! Making any changes to this file may cause the DonDominio WHMCS Module  !!
 * !! to stop working or register invalid domains/information.                 !!
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
 * @package DonDominioWHMCS
 *
 * @license CC BY-ND 3.0 <http://creativecommons.org/licenses/by-nd/3.0/>
 *
 * @link https://github.com/dondominio/dondominiowhmcs
 */

// .ES
$additionaldomainfields[".es"][] = [
    "Name"    => "OwnerType",
    "LangVar" => "estldowneridtype",
    "Type"    => "dropdown",
    "Options" => [
        "DNI|Particular o Autónomo (DNI/NIF/NIE)",
        "CIF|Sociedad (CIF)",
        "NONESCUSTOMER|Non-ES customer (National ID Card or Passport)",
        "NONESCOMPANY|Non-ES company (Company tax ID)",
    ]
];

$additionaldomainfields[".es"][] = [
    "Name"     => "ID Form Number",
    "LangVar"  => "estldidformnum",
    "Type"     => "text",
    "Size"     => "30",
    "Default"  => "",
    "Required" => true
];

$additionaldomainfields['.es'][] = [
    'Name'     => 'Administrative Document Number',
    'LangVar'  => 'es_admin_vat',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];

$additionaldomainfields[".es"][] = [
    "Name"    => "AdminType",
    "LangVar" => "estldadminidtype",
    "Type"    => "dropdown",
    "Options" => [
        "DNI|Particular o Autónomo (DNI/NIF/NIE)",
        "NONES|Non-ES customer (National ID Card or Passport)"
    ]
];

$additionaldomainfields[".es"][] = [
    "Name"    => "Info",
    "LangVar" => "es_info",
    "Type"    => "display",
    "Default" =>
    "<strong>INFORMACION SOBRE LOS CONTACTOS</strong>
        <ul>
            <li><strong>TITULAR</strong>: El contacto TITULAR si puede ser registrado con el CIF de una empresa.</li>
            <li><strong>ADMINISTRATIVO</strong>: Si el contacto administrativo reside en España, este deberá ser una persona física, no una empresa</li>
        </ul>"
];

$additionaldomainfields['.es'][] = [
    'Name'   => 'ID Form Type',
    'Remove' => true
];

if ( array_key_exists( ".es", $additionaldomainfields ) ) {
    $additionaldomainfields[".com.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".org.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".nom.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".gob.es"] = $additionaldomainfields['.es'];
    $additionaldomainfields[".edu.es"] = $additionaldomainfields['.es'];
}

// .BARCELONA, .CAT, .PL, .SCOT, .EUS, .GAL, .QUEBEC
$additionaldomainfields['.barcelona'][] = [
    'Name'     => 'Intended Use',
    'LangVar'  => 'barcelona_intendeduse',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.cat'][] = [
    'Name'     => 'Intended Use',
    'LangVar'  => 'cat_intendeduse',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.pl'][] = [
    'Name'     => 'Intended Use',
    'LangVar'  => 'pl_intendeduse',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.scot'][] = [
    'Name'     => 'Intended Use',
    'LangVar'  => 'scot_intendeduse',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.eus'][] = [
    'Name'     => 'Intended Use',
    'LangVar'  => 'eus_intendeduse',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.gal'][] = [
    'Name'     => 'Intended Use',
    'LangVar'  => 'gal_intendeduse',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];

// .HK
$additionaldomainfields['.hk'][] = [
    'Name'     => 'Birthdate',
    'LangVar'  => 'hk_birthdate',
    'Type'     => 'text',
    'Size'     => 16,
    'Default'  => '1900-01-01',
    'Required' => true
];

// .JOBS
$additionaldomainfields['.jobs'][] = [
    'Name'   => 'Website',
    "Remove" => true
];

// .LAWYER, .ATTORNEY, .DENTIST, .AIRFORCE, .ARMY, .NAVY
$contactInfoField = [
    'Name'     => 'Contact Info',
    'LangVar'  => 'lawyer_contactinfo',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.lawyer'][]   = $contactInfoField;
$additionaldomainfields['.attorney'][] = $contactInfoField;
$additionaldomainfields['.dentist'][]  = $contactInfoField;
$additionaldomainfields['.airforce'][] = $contactInfoField;
$additionaldomainfields['.army'][]     = $contactInfoField;
$additionaldomainfields['.navy'][]     = $contactInfoField;

// .LTDA
$additionaldomainfields['.ltda'][] = [
    'Name'     => 'Authority',
    'LangVar'  => 'ltda_authority',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => false
];
$additionaldomainfields['.ltda'][] = [
    'Name'     => 'License Number',
    'LangVar'  => 'ltda_license',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => false
];

// .RU
$additionaldomainfields['.ru'][] = [
    'Name'     => 'Birthdate',
    'LangVar'  => 'ru_birthdate',
    'Type'     => 'text',
    'Size'     => 16,
    'Default'  => '1900-01-01',
    'Required' => false
];
$additionaldomainfields['.ru'][] = [
    'Name'     => 'Issuer',
    'LangVar'  => 'ru_issuer',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => false
];
$additionaldomainfields['.ru'][] = [
    'Name'     => 'Issue Date',
    'LangVar'  => 'ru_issuedate',
    'Type'     => 'text',
    'Size'     => 16,
    'Default'  => '1900-01-01',
    'Required' => false
];

// .TRAVEL
$additionaldomainfields['.travel'][] = [
    'Name' => 'Trustee Service',
    'Remove' => true
];

$additionaldomainfields['.travel'][] = [
    'Name' => '.TRAVEL UIN Code',
    'Remove' => true
];

$additionaldomainfields['.travel'][] = [
    'Name' => 'Trustee Service Agreement ',
    'Remove' => true
];

$additionaldomainfields['.travel'][] = [
    'Name' => '.TRAVEL Usage Agreement',
    'Remove' => true
];

// .LAW, .ABOGADO
$additionaldomainfields['.law'][] = [
    'Name'     => 'Accreditation ID',
    'LangVar'  => 'law_accid',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name'     => 'Accreditation Body',
    'LangVar'  => 'law_accbody',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name'     => 'Accreditation Year',
    'LangVar'  => 'law_accyear',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name'     => 'Country',
    'LangVar'  => 'law_acccountry',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.law'][] = [
    'Name'     => 'State/Province',
    'LangVar'  => 'law_accprovince',
    'Type'     => 'text',
    'Size'     => 50,
    'Default'  => '',
    'Required' => true
];
$additionaldomainfields['.abogado'] = $additionaldomainfields['.law'];
