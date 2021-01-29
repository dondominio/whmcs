<?php

namespace WHMCS\Module\Addon\Dondominio\Services\Contracts;

interface EmailService_Interface
{
    public function sendNewTldsEmail(array $tlds);
    public function sendUpdatedTldsEmail(array $tlds);
}