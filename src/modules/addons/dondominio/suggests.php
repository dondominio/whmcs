<?php

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'init.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'lib', 'autoloader.php']);

use WHMCS\Module\Addon\Dondominio\App;
use Exception;

try {
    if (App::getInstance()->getService('settings')->getSetting('suggests_enabled') == 0) {
        throw new Exception('Suggests not enabled', 2);
    }

    if (!array_key_exists('uid', $_SESSION) || empty($_SESSION['uid'])) {
        $captcha = md5($_REQUEST['captcha']);

        if ($captcha != $_SESSION['captchaValue']) {
            throw new Exception('captcha_failed', 1);
        }
    }

    $response = App::getInstance()->getService('whmcs')->getDomainSuggestions($_REQUEST['text']);
} catch (Exception $e) {
    $response = [
        'error' => $e->getCode(),
        'reason' => $e->getMessage()
    ];
}

die(json_encode($response));