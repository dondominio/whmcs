<?php

namespace WHMCS\Module\Addon\Dondominio\Models;

class SSLProduct_Model extends AbstractModel
{
    const SSL_MODULE_NAME = 'dondominiossl';

    const VALIDATION_TYPE_DV = 'dv';
    const VALIDATION_TYPE_OV = 'ov';
    const VALIDATION_TYPE_EV = 'ev';


    const PRICE_INCREMENT_TYPE_PERCENTAGE = 'PERCENTAGE';
    const PRICE_INCREMENT_TYPE_FIX = 'FIX';
    const PRICE_INCREMENT_TYPE_NONE = '';

    const CUSTOM_FIELD_CERTIFICATE_ID = 'CertificateID';

    protected $table = 'mod_dondominio_ssl_products';
    protected $primaryKey = 'dd_product_id';

    public $timestamps = false;

    public static function getPriceIncrementTypes(): array
    {
        return [
            static::PRICE_INCREMENT_TYPE_FIX,
            static::PRICE_INCREMENT_TYPE_PERCENTAGE,
            static::PRICE_INCREMENT_TYPE_NONE,
        ];
    }

    public static function getValidationTypes(): array
    {
        $app = \WHMCS\Module\Addon\Dondominio\App::getInstance();

        return [
            static::VALIDATION_TYPE_DV => $app->getLang('ssl_validation_type_dv'),
            static::VALIDATION_TYPE_OV => $app->getLang('ssl_validation_type_ov'),
            static::VALIDATION_TYPE_EV => $app->getLang('ssl_validation_type_ev'),
        ];
    }

    public static function getCustomFields(): array
    {
        return [
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

        if (empty($currencyID)) {
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

    public function updateWhmcsProduct(int $groupID, string $name, int $vatNumberID): void
    {
        $whmcsProduct = $this->getWhmcsProduct();

        if (is_null($whmcsProduct)) {
            $this->createWhmcsProduct($groupID, $name, $vatNumberID);
            return;
        }

        $whmcsProduct->gid = $groupID;
        $whmcsProduct->name = $name;
        $whmcsProduct->save();

        $this->updateWhmcsProductPrice();
    }

    public function createWhmcsProduct(int $groupID, string $name, int $vatNumberID): void
    {
        $price = $this->getWhmcsProductCreatePriceCalc();
        $currencyID = static::getCurrencyID();
        $command = 'AddProduct';

        $pricing = [$currencyID => [
            'monthly' => -1,
            'quarterly' => -1,
            'semiannually' => -1,
            'annually' => $price,
            'biennially' => -1,
            'triennially' => -1,
        ]];
        $paytype = 'recurring';

        if ($this->is_trial) {
            $pricing = [$currencyID => ['monthly' => $price]];
            $paytype = 'onetime';
        }

        if ($price <= 0) {
            $paytype = 'free';
        }

        $postData = [
            'type' => 'other',
            'gid' => $groupID,
            'name' => $name,
            'welcomeemail' => '0',
            'paytype' => $paytype,
            'hidden' => false,
            'module' => static::SSL_MODULE_NAME,
            'autosetup' => 'on',
            'configoption1' => $this->dd_product_id,
            'configoption2' => $vatNumberID,
            'pricing' => $pricing,
            'showdomainoptions' => true
        ];

        $results = localAPI($command, $postData);

        if ($results['result'] === 'error') {
            throw new \Exception($results['message']);
        }

        $this->tblproducts_id = $results['pid'];
        $this->createCustomFields();

        if ($this->is_trial) {
            $whmcsProduct = $this->getWhmcsProduct();
            $whmcsProduct->daysAfterSignUpUntilAutoTermination = 365;
            $whmcsProduct->save();
        }
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
            'annually' => $this->getWhmcsProductCreatePriceCalc()
        ]);
    }

    public function getWhmcsProductAnnuallyPrice(): float
    {
        $whmcsProduct = $this->getWhmcsProduct();

        if (is_null($whmcsProduct)) {
            return 0;
        }

        $currencyID = static::getCurrencyID();

        $price = \WHMCS\Database\Capsule::table('tblpricing')->where([
            'type' => 'product',
            'relid' => $whmcsProduct->id,
            'currency' => $currencyID,
        ])->first();

        if (empty($price)) {
            return 0;
        }

        if ($price->annually < 0) {
            return 0;
        }

        return (float) $price->annually;
    }

    public function hasWhmcsProduct(): bool
    {
        return is_object($this->getWhmcsProduct());
    }

    public function getDisplayValidationType(): string
    {
        $types = static::getValidationTypes();

        if (isset($types[$this->validation_type])) {
            return $types[$this->validation_type];
        }

        return $this->validation_type;
    }

    public function getDisplayPriceIncrement(): string
    {
        $increment = $this->price_create_increment;
        $incrementType = $this->price_create_increment_type;
        $displayIncrements = [
            static::PRICE_INCREMENT_TYPE_FIX => '€',
            static::PRICE_INCREMENT_TYPE_PERCENTAGE => '%',
            static::PRICE_INCREMENT_TYPE_NONE => '',
        ];

        if (isset($displayIncrements[$incrementType])) {
            return $increment . $displayIncrements[$incrementType];
        }

        return $increment;
    }
}
