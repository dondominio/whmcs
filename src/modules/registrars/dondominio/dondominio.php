<?php
/**
 * DonDominio/MrDomain Registrar Module for WHMCS
 * This module uses the {@see https://dev.dondominio.com/whmcs/docs/plugin/ DonDominio API}
 * to perform tasks like registering, transfering, and updating domains from
 * WHMCS.
 * @link https://github.com/dondominio/dondominiowhmcs
 * @package DonDominioWHMCS
 * @license CC BY-ND 3.0 <http://creativecommons.org/licenses/by-nd/3.0/>
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Registrar\Dondominio\App;
use Exception;

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

/**
 * Define module related metadata
 *
 * Provide some module information including the display name and API Version to
 * determine the method of decoding the input values.
 *
 * @return array
 */
function dondominio_MetaData()
{
    $app = new App();

    return [
        'DisplayName' => $app->getName(),
        'APIVersion' => $app->getVersion()
    ];
}

/**
 * Define registrar configuration options.
 *
 * The values you return here define what configuration options
 * we store for the module. These values are made available to
 * each module function.
 *
 * You can store an unlimited number of configuration settings.
 * The following field types are supported:
 *  * Text
 *  * Password
 *  * Yes/No Checkboxes
 *  * Dropdown Menus
 *  * Radio Buttons
 *  * Text Areas
 *
 * @return array
 */
function dondominio_getConfigArray()
{
    try {
        $app = new App();
        $response = $app->dispatchAction(__FUNCTION__);

        return $response;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Register a domain.
 *
 * Attempt to register a domain with the domain registrar.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain registration order
 * * When a pending domain registration order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_RegisterDomain($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Initiate domain transfer.
 *
 * Attempt to create a domain transfer request for a given domain.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain transfer order
 * * When a pending domain transfer order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_TransferDomain($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Renew a domain.
 *
 * Attempt to renew/extend a domain for a given number of years.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain renewal order
 * * When a pending domain renewal order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_RenewDomain($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Fetch current nameservers.
 *
 * This function should return an array of nameservers for a given domain.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_GetNameservers($params)
{
    try {
        $app = new App($params);
        $nameservers = $app->dispatchAction(__FUNCTION__, $params);

        return $nameservers;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Save nameserver changes.
 *
 * This function should submit a change of nameservers request to the
 * domain registrar.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_SaveNameservers($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Get the current WHOIS Contact Information.
 *
 * Should return a multi-level array of the contacts and name/address
 * fields that be modified.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_GetContactDetails($params)
{
    try {
        $app = new App($params);
        $contactDetails = $app->dispatchAction(__FUNCTION__, $params);

        return $contactDetails;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Update the WHOIS Contact Information for a given domain.
 *
 * Called when a change of WHOIS Information is requested within WHMCS.
 * Receives an array matching the format provided via the `GetContactDetails`
 * method with the values from the users input.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_SaveContactDetails($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Check Domain Availability.
 *
 * Determine if a domain or group of domains are available for
 * registration or transfer.
 *
 * @param array $params common module parameters
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @see \WHMCS\Domains\DomainLookup\SearchResult
 * @see \WHMCS\Domains\DomainLookup\ResultsList
 *
 * @throws Exception Upon domain availability check failure.
 *
 * @return \WHMCS\Domains\DomainLookup\ResultsList An ArrayObject based collection of \WHMCS\Domains\DomainLookup\SearchResult results
 */
function dondominio_CheckAvailability($params)
{
    try {
        $app = new App($params);
        $results = $app->dispatchAction(__FUNCTION__, $params);

        return $results;
    } catch (Exception $e) {
        // BUILD UNKNOWN RESPONSE
        $unknownDomain = new \WHMCS\Domains\DomainLookup\SearchResult($params['searchTerm'], $params['tlds'][0]);
        $unknownDomain->setStatus(\WHMCS\Domains\DomainLookup\SearchResult::STATUS_UNKNOWN);

        $results = new \WHMCS\Domains\DomainLookup\ResultsList();
        $results->append($unknownDomain);

        return $results;
    }
}

/**
 * Domain Suggestion Settings.
 *
 * Defines the settings relating to domain suggestions (optional).
 * It follows the same convention as `getConfigArray`.
 *
 * @see https://developers.whmcs.com/domain-registrars/check-availability/
 *
 * @return array of Configuration Options
 */
function dondominio_DomainSuggestionOptions()
{
    try {
        $app = new App();
        $results = $app->dispatchAction(__FUNCTION__);

        return $results;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Get Domain Suggestions.
 *
 * Provide domain suggestions based on the domain lookup term provided.
 *
 * @param array $params common module parameters
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @see \WHMCS\Domains\DomainLookup\SearchResult
 * @see \WHMCS\Domains\DomainLookup\ResultsList
 *
 * @throws Exception Upon domain suggestions check failure.
 *
 * @return \WHMCS\Domains\DomainLookup\ResultsList An ArrayObject based collection of \WHMCS\Domains\DomainLookup\SearchResult results
 */
function dondominio_GetDomainSuggestions($params)
{
    try {
        $app = new App($params);
        $results = $app->dispatchAction(__FUNCTION__, $params);

        return $results;
    } catch (Exception $e) {
        return new \WHMCS\Domains\DomainLookup\ResultsList();
    }
}

/**
 * Get registrar lock status.
 *
 * Also known as Domain Lock or Transfer Lock status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return string|array Lock status or error message
 */
function dondominio_GetRegistrarLock($params)
{
    try {
        $app = new App($params);
        $locked = $app->dispatchAction(__FUNCTION__, $params);

        return $locked;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Set registrar lock status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_SaveRegistrarLock($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Enable/Disable ID Protection.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_IDProtectToggle($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Request EEP Code.
 *
 * Supports both displaying the EPP Code directly to a user or indicating
 * that the EPP Code will be emailed to the registrant.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 *
 */
function dondominio_GetEPPCode($params)
{
    try {
        $app = new App($params);
        $eppCode = $app->dispatchAction(__FUNCTION__, $params);

        return [
            'eppcode' => $eppCode
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Register a Nameserver.
 *
 * Adds a child nameserver for the given domain name.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_RegisterNameserver($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Modify a Nameserver.
 *
 * Modifies the IP of a child nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_ModifyNameserver($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Delete a Nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_DeleteNameserver($params)
{
    try {
        $app = new App($params);
        $app->dispatchAction(__FUNCTION__, $params);

        return [
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Sync Domain Status & Expiration Date.
 *
 * Domain syncing is intended to ensure domain status and expiry date
 * changes made directly at the domain registrar are synced to WHMCS.
 * It is called periodically for a domain.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dondominio_Sync($params)
{
    try {
        $app = new App($params);
        $response = $app->dispatchAction(__FUNCTION__, $params);

        return $response;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Incoming Domain Transfer Sync.
 *
 * Check status of incoming domain transfers and notify end-user upon
 * completion. This function is called daily for incoming domains.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 * @see https://developers.whmcs.com/domain-registrars/domain-syncing/
 *
 * @return array
 */
function dondominio_TransferSync($params)
{
    try {
        $app = new App($params);
        $response = $app->dispatchAction(__FUNCTION__, $params);

        return $response;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Client Area Custom Button Array.
 *
 * Allows you to define additional actions your module supports.
 * In this example, we register a Push Domain action which triggers
 * the `registrarmodule_push` function when invoked.
 *
 * @return array
 */
function dondominio_ClientAreaCustomButtonArray($params)
{
    try {
        $app = new App($params);
        $response = $app->dispatchAction(__FUNCTION__, $params);

        return $response;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/*************************
 * CUSTOM ACTIONS
 *************************/

function dondominio_WhoisPrivacy($params)
{
    try {
        $app = new App($params);
        $response = $app->dispatchAction(__FUNCTION__, $params);

        return $response;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}