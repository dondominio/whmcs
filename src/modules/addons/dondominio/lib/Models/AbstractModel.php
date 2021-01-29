<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    public static function getTableName()
    {
        return (new static())->getTable();
    }
}