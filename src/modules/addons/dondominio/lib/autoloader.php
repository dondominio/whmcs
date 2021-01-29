<?php

spl_autoload_register(function($class) {
    $prefix = 'WHMCS\Module\Addon\Dondominio';
    $prefixLength = strlen($prefix);
    $basePath = __DIR__;
    if (0 === strncmp($prefix, $class, $prefixLength)) {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $prefixLength));
        $file = realpath($basePath . (empty($file) ? '' : DIRECTORY_SEPARATOR) . $file . '.php');
        if( @file_exists($file)) {
            require_once($file);
        }
    }
});