<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

class Settings_Model extends AbstractModel
{
    protected $table = 'mod_dondominio_settings';
    protected $primaryKey = 'key';
    protected $keyType = 'string';

    public $incrementing = false;
    public $timestamps = false;
}