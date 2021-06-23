<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

class SSLProduct_Model extends AbstractModel
{
    const SSL_MODULE_NAME = 'dondominiossl';

    const PRICE_INCREMENT_TYPE_PERCENTAGE = 'PERCENTAGE';
    const PRICE_INCREMENT_TYPE_FIX = 'FIX';
    const CUSTOM_FIELD_CSR = 'CSR';
    const CUSTOM_FIELD_ADMIN_ID = 'Admin User ID';
    const CUSTOM_FIELD_CERTIFICATE_ID = 'CertificateID';

    protected $table = 'mod_dondominio_ssl_products';
    protected $primaryKey = 'dd_product_id';

    public $timestamps = false;

    public static function getPriceIncrementTypes(): array
    {
        return [
            static::PRICE_INCREMENT_TYPE_FIX,
            static::PRICE_INCREMENT_TYPE_PERCENTAGE,
        ];
    }

    public static function getCustomFields(): array
    {
        return [
            static::CUSTOM_FIELD_CSR => [
                'type' => 'textarea',
                'required' => true,
                'showorder' => true
            ],
            static::CUSTOM_FIELD_ADMIN_ID => [
                'type' => 'text',
                'required' => true,
                'showorder' => true
            ],
            static::CUSTOM_FIELD_CERTIFICATE_ID => [
                'type' => 'text',
                'required' => false,
                'showorder' => false
            ],
        ];
    }

    protected static function getCurrencyID(): int
    {
        $currencyID = \WHMCS\Database\Capsule::table('tblcurrencies')
            ->where(['code' => 'EUR'])
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->value('id');

        if (empty($currencyID)){
            throw new \Exception('currency_error');
        }

        return (int) $currencyID;
    }

    public function getWhmcsProduct()
    {
        if (empty($this->tblproducts_id)) {
            return null;
        }

        return \WHMCS\Product\Product::where(['id' => $this->tblproducts_id])->first();
    }

    public function updateWhmcsProduct(int $groupID, string $name): void
    {
        $whmcsProduct = $this->getWhmcsProduct();

        if (is_null($whmcsProduct)) {
            $this->createWhmcsProduct($groupID, $name);
            return;
        }

        $whmcsProduct->gid = $groupID;
        $whmcsProduct->name = $name;
        $whmcsProduct->save();

        $this->updateWhmcsProductPrice();
    }

    public function createWhmcsProduct(int $groupID, string $name): void
    {
        $currencyID = static::getCurrencyID();
        $command = 'AddProduct';
        $postData = [
            'type' => 'other',
            'gid' => $groupID,
            'name' => $name,
            'welcomeemail' => '0',
            'paytype' => 'onetime',
            'module' => static::SSL_MODULE_NAME,
            'autosetup' => 'on',
            'configoption1' => $this->dd_product_id,
            'pricing' => [$currencyID => ['monthly' => $this->getWhmcsProductCreatePriceCalc()]],
        ];

        $results = localAPI($command, $postData);

        if ($results['result'] === 'error') {
            throw new \Exception($results['message']);
        }

        $this->tblproducts_id = $results['pid'];
        $this->createCustomFields();
    }

    public function getWhmcsProductCreatePriceCalc(): float
    {
        $type = $this->price_create_increment_type;
        $price_create = $this->price_create;
        $increment = $this->price_create_increment;

        if ($type ===  static::PRICE_INCREMENT_TYPE_FIX) {
            return (float) $price_create + $increment;
        }

        if ($type ===  static::PRICE_INCREMENT_TYPE_PERCENTAGE) {
            return (float) $price_create + (($increment / 100) * $price_create);
        }

        return (float) $price_create;
    }

    public function createCustomFields(): void
    {
        if (empty($this->tblproducts_id)) {
            return;
        }

        $customFields = static::getCustomFields();

        foreach ($customFields as $key => $cf) {
            $customField = new \WHMCS\CustomField();

            $customField->type = 'product';
            $customField->relid = $this->tblproducts_id;
            $customField->fieldname = $key;
            $customField->fieldtype = $cf['type'];
            $customField->required = $cf['required'] ? 'on' : '';
            $customField->showorder = $cf['showorder'] ? 'on' : '';

            $customField->save();
        }
    }

    public function updateWhmcsProductPrice(): void
    {
        $whmcsProduct = $this->getWhmcsProduct();
        $currencyID = static::getCurrencyID();

        if (is_null($whmcsProduct)) {
            return;
        }

        \WHMCS\Database\Capsule::table('tblpricing')->updateOrInsert([
            'type' => 'product',
            'relid' => $whmcsProduct->id,
            'currency' => $currencyID,
        ], [
            'monthly' => $this->getWhmcsProductCreatePriceCalc()
        ]);
    }
}
