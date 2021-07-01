<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

class SSLCertificateOrder_Model extends AbstractModel
{
    protected $table = 'mod_dondominio_ssl_certificate_orders';
    protected $primaryKey = 'certificate_id';

    public $timestamps = false;

    public function getService()
    {
        $service = \WHMCS\Service\Service::where(['id' => $this->tblhosting_id])->first();
        
        if (is_object($service)){
            return $service;
        }

        return null;
    }
}
