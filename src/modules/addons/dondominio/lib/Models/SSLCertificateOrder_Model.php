<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

class SSLCertificateOrder_Model extends AbstractModel
{
    const STATUS_PROCESS = 'process';
    const STATUS_VALID = 'valid';
    const STATUS_EXPIRED = 'expired';
    const STATUS_RENEW = 'renew';
    const STATUS_REISSUE = 'reissue';
    const STATUS_CANCEL = 'cancel';

    protected $table = 'mod_dondominio_ssl_certificate_orders';
    protected $primaryKey = 'certificate_id';

    public $timestamps = false;

    /**
     * Return certificates status list
     * 
     * @return array
     */
    public static function getStatus(): array
    {
        $app = \WHMCS\Module\Addon\Dondominio\App::getInstance();

        return [
            static::STATUS_PROCESS => $app->getLang('ssl_certificate_status_process'),
            static::STATUS_VALID => $app->getLang('ssl_certificate_status_valid'),
            static::STATUS_EXPIRED => $app->getLang('ssl_certificate_status_expired'),
            static::STATUS_RENEW => $app->getLang('ssl_certificate_status_renew'),
            static::STATUS_REISSUE => $app->getLang('ssl_certificate_status_reissue'),
            static::STATUS_CANCEL => $app->getLang('ssl_certificate_status_cancel'),
        ];
    }

    /**
     * Gets the Service (tblhosting) related to the certificate
     * 
     * @return object
     */
    public function getService()
    {
        $service = \WHMCS\Service\Service::where(['id' => $this->tblhosting_id])->first();

        if (is_object($service)) {
            return $service;
        }

        return null;
    }

    /**
     * Gets the certificate client details
     * 
     * @return array
     */
    public function getClientDetails(): array
    {
        $service = $this->getService();

        if (!is_object($service)) {
            return [];
        }

        $command = 'GetClientsDetails';
        $postData = ['clientid' => $service->clientId];

        return localAPI($command, $postData);
    }
}
