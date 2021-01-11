<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use Exception;

class SaveRegistrarLock extends Action
{
    protected $errors = [];
    protected $domain = '';
    protected $lockable = false;

    public function __invoke()
    {
        $response = $this->app->getService('api')->getDomainInfo($this->domain, 'status');

        $this->lockable = array_key_exists("transferBlock", $response->getResponseData());
        
        if ($this->params["lockenabled"] == 'locked') {
            $this->updateTransferBlock(true);
            $this->updateBlock(true);
        } else {
            $this->updateBlock(false);
            $this->updateTransferBlock(false);
        }

        if (!$this->lockable) {
            throw new Exception('This domain extension does not allow domain transfer locking.');
        }

        if (count($this->errors) > 0) {
            throw new Exception(implode("; ", $this->errors));
        }
    }

    private function updateTransferBLock($block)
    {
        if (!$this->lockable) {
            return;
        }

        try {
            $this->app->getService('api')->updateDomain($this->domain, [
                'updateType' => 'transferBlock',
                'transferBlock' => $block
            ]);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    private function updateBlock($block)
    {
        if (!$this->params['blockAll'] == 'on') {
            return;
        }

        try {
            $this->app->getService('api')->updateDomain($this->domain, [
                'updateType' => 'block',
                'block' => $block
            ]);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}