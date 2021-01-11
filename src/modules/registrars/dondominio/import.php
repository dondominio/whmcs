<?php

/**
 * DonDominio Domain Importer for WHMCS
 * Synchronization tool for domains in DonDomino accounts and WHMCS.
 * @copyright Soluciones Corporativas IP, SL 2015
 * @package DonDominioWHMCSImporter
 */

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'init.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'lib', 'autoloader.php']);

use WHMCS\Module\Registrar\Dondominio\Cli\Arguments;
use WHMCS\Module\Registrar\Dondominio\Cli\Output;
use WHMCS\Module\Registrar\Dondominio\App;
use WHMCS\Module\Registrar\Dondominio\Services\Import_Service;
use WHMCS\Module\Registrar\Dondominio\Services\API_Service;

$arguments = new Arguments();

$arguments->addOption(['username', 'u'], null, 'DonDominio API Username (Required)');
$arguments->addOption(['password', 'p'], null, 'DonDominio API Password (Required)');
$arguments->addOption('uid', null, 'Default Client Id (Required)');
$arguments->addOption(['output', 'o'], "php://stdout", 'Filename to output data - Defaults to STDOUT');

$arguments->addFlag('forceUID', 'Use the default Client Id for all domains');
$arguments->addFlag('dry', 'Do not make any changes to the database');
$arguments->addFlag(['verbose', 'v'], 'Display extra output');
$arguments->addFlag(['debug', 'd'], 'Display cURL debug information');
$arguments->addFlag(['silent', 's'], 'No output');
$arguments->addFlag('version', 'Version information');
$arguments->addFlag(['help', 'h'], 'This information');

$arguments->parse();

//¿Enable Silent mode?
if($arguments->get('silent')){
	Output::setSilent(true);
}

//Set output file/method
Output::setOutput($arguments->get('output'));

//Check required arguments
//If an argument is missing, show help screen.
//Also show help screen with --help (-h) flag.
if(
	(
		!$arguments->get('username') ||
		!$arguments->get('password') ||
		!$arguments->get('uid') ||
		$arguments->get('help')
	) &&
	!$arguments->get('version')
){
	$arguments->helpScreen();
	
	Output::line("");
	
	exit();
}

//Display version information
if($arguments->get('version')){
	Output::debug("Version information requested");
	
	Import_Service::displayVersion();
	exit();
}

//¿Is the "verbose" flag set?
//If so, enable verbose mode
if($arguments->get('verbose')){
	Output::setDebug(true);
}

/*
 * Init DD API SDK
 */
Output::debug("Initializing Services");

$app = new App();

//The DonDominio API Client
$api = new API_Service([
    'endpoint' => 'https://simple-api-test.dondominio.net',
    'port' => 443,
    'apiuser' => $arguments->get('username'),
    'apipasswd' =>  $arguments->get('password'),
    'debug' => ($arguments->get('debug') && !$arguments->get('silent')) ? true : false,
    'autoValidate' => true,
    'versionCheck' => true,
    'response' => [
        'throwExceptions' => false
    ]
], $app);
$app->setAPIService($api);

$import = new Import_Service([
	'clientId' => $arguments->get('uid'),			//Default WHMCS client ID
	'dryrun' => $arguments->get('dry'),				//Dry run - makes no changes to database
	'forceClientId' => $arguments->get('forceUID')	//Always use default WHMCS Client ID for all operations
], $app);
$app->setImportService($import);

/*
 * Start sinchronization.
 */
Output::debug("Initializing Sync");

$app->getService('import')->sync();