<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class WhoisPrivacy extends Action
{
    public function __invoke()
    {
        $errors = [];

        if (array_key_exists('ok', $_POST) && $_POST['ok'] === 'ok') {
            try {
                $this->app->getService('api')->updateDomain($this->domain, [
                    'updateType' => 'whoisPrivacy',
                    'whoisPrivacy' => ($_POST['privacy'] == 'on')
                ]);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        try {
            $status = $this->app->getService('api')->getDomainInfo($this->domain, 'status');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        $result = [
            'templatefile' => 'whoisprivacy',
            'breadcrumb' => [
                'clientarea.php?action=domaindetails&domainid='.$this->params['domainid'].'&modop=custom&a=whoisPrivacy' => 'WHOIS Privacy'
            ]
        ];

        if (count($errors) != 0) {
            $result['vars'] = [
                'error' => implode(', ', $errors)
            ];
        } else {
            $result['vars'] = [
                'status' => $status->get('whoisPrivacy')
            ];
        }

        return $result;
    }
}