<?php

namespace WHMCS\Module\Registrar\Dondominio\Services;

use WHMCS\Module\Registrar\Dondominio\Cli\Output;
use WHMCS\Module\Registrar\Dondominio\Services\Contracts\ImportService_Interface;
use Exception;

class Import_Service implements ImportService_Interface
{
    const VERSION = '2.0';

    /**
	 * Array of options used by this class.
	 * @var array
	 */
	protected $options = [
		'clientId' => '',
		'dryrun' => false,
		'forceClientId' => false
	];

	protected $app = null;

	/**
	 * Initialize sync.
	 * Applies the options provided and checks for any missing
	 * or invalid parameters.
	 * @param array $options Options
	 * @param WHMCS\Module\Registrar\Dondominio\App $app
	 */
	public function __construct(array $options = [], $app)
	{
		$this->options = array_merge(
			$this->options,
			(is_array($options)) ? $options : []
		);

		$this->app = $app;

        Output::debug("Checking valid Client ID");

		if (!$this->options['clientId']) {
			Output::error("You must specify a valid Client ID to continue.");
        }

        Output::debug("Searching Client ID in database");

		if (!$this->getApp()->getService('whmcs')->clientExistsById($options['clientId'])) {
			Output::error('Client could not be found. Provide a valid Client ID using the --uid parameter.');
		}

		if ($this->options['dryrun']) {
			Output::debug("--dry flag found, enabling Dry Run mode");
			Output::line("*** DRY RUN MODE ***");
			Output::line("No changes will be made to your database.");
		}
	}

	/**
     * Gets App
     *
     * @return WHMCS\Module\Registrar\Dondominio\App
     */
	public function getApp()
	{
		return $this->app;
	}

	/**
	 * Sync domains.
	 * Creates all missing domains in the local database comparing it against
	 * the DonDominio account associated to the API Username provided.
	 *
	 * Does not return anything, writes directly to output.
	 */
	public function sync()
	{
		Output::debug("Sync start");

		$apiService = $this->getApp()->getService('api');
		$whmcsService = $this->getApp()->getService('whmcs');

		$total = $results = $created = $exists = $error = 0;

		$error_list = [];

		Output::debug("Getting domains from API");

		$domains = $apiService->getDomainList();
		$order = null;

		Output::debug("Looping through " . count($domains) . " domains");

		foreach ($domains as $domain) {
			// CHECK DOMAIN EXISTS

			if ($whmcsService->domainExists($domain['name'])) {
				Output::debug("Domain " . $domain['name'] . " already on database. Do nothing.");
				$exists++;
				continue;
			}

			// TLD DOESNT EXIST

			if (!$whmcsService->tldExists($domain['tld'])) {
				Output::line(str_pad($domain['name'], 40, " ") . "TLD not configured (" . $domain['tld'] . ")");
				$error_list['tld_' . $domain['tld'] . '_notfound'] = 'You need to configure the ' . $domain['tld'] . ' TLD in WHMCS to sync .' . $domain['tld'] . ' domains.';
				$error++;
				continue;
			}

			// SEARCH USER

			Output::debug("Searching domain owner for " . $domain['name']);

			$userid = $this->options['clientId'];

			if (!$this->options['forceClientId']) {
				try {
					$response = $apiService->getDomainInfo($domain['name'], 'contact');
					$contactOwner = $response->get('contactOwner');
					$user = $whmcsService->findClientByEmail($contactOwner['email']);

					if (is_object($user)) {
						$userid = $user->id;
					}
				} catch (Exception $e) {
					Output::line("An error occurred while getting the domain's owner. Can't continue.");
					Output::line("");

					return false;
				}
			}

			if (!$this->options['dryrun']) {

				// CREATE ORDER (if necessary)

				try {
					if (is_null($order)) {
						Output::debug("Creating order in database to hold domains");
						$order = $whmcsService->createOrder($userid);
					}
				} catch (Exception $e) {
					Output::line("An error ocurred while creating the order: " . $e->getMessage());
					Output::line("");

					return false;
				}

				// CREATE DOMAIN

				try {
					Output::debug('Requesting specific domain info to API ' . $domain['name']);
					$response = $apiService->getDomainInfo($domain['name'], 'status');

					Output::debug("Creating domain " . $response->get('name'));
					$whmcsService->createDomain($order->id, $userid, $response);

					Output::line(str_pad($response->get('name'), 30, " ") . "Created");
					$created++;
				} catch (Exception $e) {
					Output::line(str_pad($response->get('name'), 30, " ") . "Error: " . $e->getMessage());
				}
			}
		}

		Output::line("");
		Output::line("Sync finished.");
		Output::line("$created domains created - $exists already exist - $error errors found");
        Output::line("");

		if (count($error_list)) {
            Output::line("The following errors were found:");

			foreach ($error_list as $error) {
				Output::line("-" . $error);
			}
		}
    }

    public static function displayVersion()
    {
        Output::line("");
        Output::line("DonDominio Registrar Module v" . static::VERSION);
        Output::line("Copyright (c) 2020 Soluciones Corporativas IP SL");
        Output::line("");
        Output::line("For usage instructions, use -h or --help");
        Output::line("");
    }
}