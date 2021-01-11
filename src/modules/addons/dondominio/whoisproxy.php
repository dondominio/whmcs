<?php

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'init.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'lib', 'autoloader.php']);

use WHMCS\Module\Addon\Dondominio\App;
use Exception;

try {
    $ip = $_SERVER['REMOTE_ADDR'];
    $domain = $_GET['domain'];

    if (empty($domain)) {
        throw new Exception('no_domain_provided');
    }

    $allowedIps = App::getInstance()->getService('settings')->getSetting('whois_ip');

    $ip_array = explode(';', $allowedIps);

    if (!in_array($ip, $ip_array)) {
        throw new Exception("Error: $ip not allowed to access this script.");
    }

    $whois = App::getInstance()->getService('whois')->doWhois($domain);

    $response = $whois['available'] ? 'DDAvailable' : 'Not Available';
} catch (Exception $e) {
    $response = $e->getMessage();
}

echo $response;