<?php

namespace WHMCS\Module\Server\Dondominiossl\Actions;


class CreateAccount
{
    protected array $params = [];
    protected \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api;

    protected string $fieldCSR = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CSR;
    protected string $fieldAdminID = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_ADMIN_ID;
    protected string $fieldCertificateID = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::CUSTOM_FIELD_CERTIFICATE_ID;

    public function __construct(
        \WHMCS\Module\Server\Dondominiossl\Services\Contracts\APIService_Interface $api,
        array $params
    ) {
        $this->api = $api;
        $this->params = $params;
    }

    public function execute(): string
    {
        if (empty($this->params['customfields'][$this->fieldCSR])){
            return 'CSR Data not found';
        }

        if (empty($this->params['customfields'][$this->fieldAdminID])){
            return 'Admin Contact ID not found';
        }

        if (empty($this->params['configoption1'])){
            return 'Product ID not found';
        }

        $args = [
            'productID' => $this->params['configoption3'],
            'csrData' => $this->params['customfields']['CSR'],
            'adminContactID' => $this->params['customfields'][$this->fieldAdminID],
        ];

        $customFieldValue = $this->getCustomFieldValue();

        if (empty($customFieldValue)){
            return sprintf('Custom Field %s not found', $this->fieldCertificateID);
        }

        try {
            $response = $this->api->createCertificate($args);
        } catch (\Exception $e){
            return $e->getMessage();
        }

        $customFieldValue->value = $response->get('ssl')['certificateID'];
        $customFieldValue->save();

        return 'success';
    }

    protected function getCustomFieldValue()
    {
        $customField = \WHMCS\CustomField::where([
            'relid' => $this->params['pid'],
            'type' => 'product',
            'fieldname' => $this->fieldCertificateID
        ])->first();

        if (empty($customField)){
            return null;
        }

        $customFieldValue = \WHMCS\CustomField\CustomFieldValue::where([
            'fieldid' => $customField->id,
            'relid' => $this->params['serviceid'],
        ])->first();

        return $customFieldValue;
    }
}
