<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


abstract class Base
{
    protected array $params = [];
    protected \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api;

    protected string $fieldCommonName = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_COMMON_NAME;
    protected string $fieldCertificateID = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID;

    public function __construct(
        \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api,
        array $params
    ) {
        $this->api = $api;
        $this->params = $params;
    }

    public abstract function execute(): string;

    protected function getCertificateIDCustomFieldValue()
    {
        $service = $this->params['model'];
        $customFieldValues = $service->customFieldValues;

        foreach ($customFieldValues as $value) {
            $customField = $value->customField;

            if ($customField->fieldName === $this->fieldCertificateID) {
                return $value;
            }
        }

        return null;
    }

    protected function getVATNumber(): string
    {
        $clientCustomFields = $this->params['clientsdetails']['customfields'];

        foreach ($clientCustomFields as $cf) {
            if ($cf['id'] === $this->params['configoption2']) {
                return $cf['value'];
            }
        }

        return '';
    }

    protected function getPeriod(): int
    {
        $service = $this->params['model'];
        $billingCycle = $service->billingcycle;

        $yearsMap = [
            'Annually' => 1,
            'Biennially' => 2,
            'Triennially' => 3,
        ];

        return isset($yearsMap[$billingCycle]) ? $yearsMap[$billingCycle] : 1;
    }
}
