<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

class TldSettings_Model extends AbstractModel
{
    protected $table = 'mod_dondominio_tld_settings';

    public $timestamps = false;

    protected $attributes = [
        'register_increase' => 0,
        'register_increase_type' => 'fixed',
        'renew_increase' => 0,
        'renew_increase_type' => 'fixed',
        'transfer_increase' => 0,
        'transfer_increase_type' => 'fixed'
    ];
}