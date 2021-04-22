<?php

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Dondominio\App;

// This is a Hook Loader, only $hooks must be modifiable

$lang = Capsule::table('tbladmins')->where('id', '=', $_SESSION['adminid'])->value('language');

$trans = [
    'spanish' => [
        'close' => 'Cerrar',
        'new_version' => 'Nueva versión!',
        'new_version_body' => 'Ya hay disponible una nueva version de el Módulo de DonDominio',
        'update' => 'Actualizar',
        'no_show' => 'No volver a mostrar',
        'actual_version' => 'Ultima versión!',
        'admin' => 'Administrar',
        'check_changelog' => 'Consulta los cambios de la nueva versión'
    ],
    'english' => [
        'close' => 'Close',
        'new_version' => 'New version!',
        'new_version_body' => 'A new version of the MrDomain/DonDominio Module is now available',
        'update' => 'Update',
        'no_show' => 'Not show again',
        'actual_version' => 'Last version!',
        'admin' => 'Admin',
        'check_changelog' => 'Check the changes of the new version',
    ]
];

$lang = array_key_exists($lang, $trans) ? $trans[$lang] : $trans['english'];

App::getInstance()->setLang($lang);

$hooks = [
    'PreCronJob',
    'AdminHomeWidgets',
    'AdminHomepage'
];

foreach ($hooks as $hook) {
    $className = 'WHMCS\Module\Addon\Dondominio\Hooks\\' . $hook;

    if (!class_exists($className)) {
        continue;
    }

    if (!method_exists($className, '__invoke')) {
        continue;
    }

    add_hook($hook, 1, new $className());
}
