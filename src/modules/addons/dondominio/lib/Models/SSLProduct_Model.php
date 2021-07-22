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
    const CUSTOM_FIELD_COMMON_NAME = 'CommonName';
    const CUSTOM_FIELD_ALT_NAME = 'altName';

    protected $table = 'mod_dondominio_ssl_products';
    protected $primaryKey = 'dd_product_id';

    public $timestamps = false;

    /**
     * Return a list with the Price Increment types
     * 
     * @return array
     */
    public static function getPriceIncrementTypes(): array
    {
        return [
            static::PRICE_INCREMENT_TYPE_FIX,
            static::PRICE_INCREMENT_TYPE_PERCENTAGE,
            static::PRICE_INCREMENT_TYPE_NONE,
        ];
    }

    /**
     * Return a list with the Validation Types
     * 
     * @return array
     */
    public static function getValidationTypes(): array
    {
        $app = \WHMCS\Module\Addon\Dondominio\App::getInstance();

        return [
            static::VALIDATION_TYPE_DV => $app->getLang('ssl_validation_type_dv'),
            static::VALIDATION_TYPE_OV => $app->getLang('ssl_validation_type_ov'),
            static::VALIDATION_TYPE_EV => $app->getLang('ssl_validation_type_ev'),
        ];
    }

    /**
     * Return a list with the Custom Fields to add to the WHMCS Product
     * 
     * @return array
     */
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

    /**
     * Return a list with the Custom Fields to add to the WHMCS Product
     * 
     * @return array
     */
    public function getCustomFields(): array
    {
        $customFields = [
            static::CUSTOM_FIELD_COMMON_NAME => [
                'type' => 'text',
                'required' => true,
                'showorder' => true,
                'regex' => '/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/',
                'translations' => [
                    'name' =>  [
                        'spanish' => 'Common Name',
                        'english' => 'Common Name',
                    ],
                    'description' => [],
                ],
            ],
            static::CUSTOM_FIELD_CERTIFICATE_ID => [
                'type' => 'text',
                'required' => false,
                'showorder' => false,
                'regex' => '',
                'translations' => [
                    'name' => [],
                    'description' => [],
                ],
            ],
        ];

        if ($this->isWildCard()) {
            $customFields[static::CUSTOM_FIELD_COMMON_NAME]['regex'] = '/^(\*\.)[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/';
            $customFields[static::CUSTOM_FIELD_COMMON_NAME]['translations']['description'] = [
                'spanish' => 'El common name para un Wildard debe empezar por un * (*.example.com)',
                'english' => 'The common name for a Wildcard must start with * (* .example.com)',
            ];
        }

        if ($this->isMultiDomain()) {
            $this->generateAltNameCustomFields($customFields);
        }

        return $customFields;
    }

    /**
     * Add the alternative names to custom fields array 
     * 
     * @param array $customField
     * 
     * @return void
     */
    protected function generateAltNameCustomFields(array &$customField): void
    {
        $numDomains = $this->getNumDomains();

        for ($i = 1; $i <= $numDomains; $i++) {
            $customField[static::CUSTOM_FIELD_ALT_NAME . $i] = [
                'type' => 'text',
                'required' => false,
                'showorder' => true,
                'regex' => '',
                'translations' => [
                    'name' =>  [
                        'spanish' => 'Nombre Alternativo ' . $i,
                        'english' => 'Alternative Name ' . $i,
                    ],
                    'description' => [],
                ],
            ];
        }
    }

    /**
     * Return the WHMCS Product asigned to this DonDominio Product
     * 
     * @return object
     */
    public function getWhmcsProduct()
    {
        if (empty($this->tblproducts_id)) {
            return null;
        }

        return \WHMCS\Product\Product::where(['id' => $this->tblproducts_id])->first();
    }

    /**
     * Update/Create the WHMCS Product
     * 
     * @return void
     */
    public function updateWhmcsProduct(int $groupID, string $name, int $vatNumberID): void
    {
        $whmcsProduct = $this->getWhmcsProduct();

        if (is_null($whmcsProduct)) {
            $this->createWhmcsProduct($groupID, $name, $vatNumberID);
            return;
        }

        $whmcsProduct->gid = $groupID;
        $whmcsProduct->name = $name;
        $whmcsProduct->configoption2 = $vatNumberID;
        $whmcsProduct->save();

        $this->updateWhmcsProductPrice();
    }

    /**
     * Create the WHMCS Product
     * 
     * @return void
     */
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
            'showdomainoptions' => false
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

    /**
     * Get the WHMCS Product price calculating the increment
     * 
     * @return float
     */
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

    /**
     * Create the WHMCS Product Custom Fields
     * 
     * @return void
     */
    public function createCustomFields(): void
    {
        if (empty($this->tblproducts_id)) {
            return;
        }

        $customFields = $this->getCustomFields();

        foreach ($customFields as $key => $cf) {
            $customField = new \WHMCS\CustomField();

            $customField->type = 'product';
            $customField->relid = $this->tblproducts_id;
            $customField->fieldname = $key;
            $customField->fieldtype = $cf['type'];
            $customField->required = $cf['required'] ? 'on' : '';
            $customField->showorder = $cf['showorder'] ? 'on' : '';
            $customField->regularExpression = $cf['regex'];

            $customField->save();

            foreach ($cf['translations'] as $type => $translations) {
                foreach ($translations as $lang => $trans) {
                    \WHMCS\Database\Capsule::table('tbldynamic_translations')->updateOrInsert([
                        'related_type' => 'custom_field.{id}.' . $type,
                        'related_id' => $customField->id,
                        'language' => $lang,
                    ], [
                        'translation' => $trans,
                        'input_type' => 'text'
                    ]);
                }
            }
        }
    }

    /**
     * Update the WHMCS Product price
     * 
     * @return void
     */
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

    /**
     * Get the WHMCS Product annually price
     * 
     * @return float
     */
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

    /**
     * Return if the DonDominio product has WHMCS Product assigned
     * 
     * @return bool
     */
    public function hasWhmcsProduct(): bool
    {
        return is_object($this->getWhmcsProduct());
    }

    /**
     * Return if the DonDominio product has WHMCS Product assigned
     * 
     * @return bool
     */
    public function getDisplayValidationType(): string
    {
        $types = static::getValidationTypes();

        if (isset($types[$this->validation_type])) {
            return $types[$this->validation_type];
        }

        return $this->validation_type;
    }

    /**
     * Return the Increment Price to display
     * 
     * @return bool
     */
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

    /**
     * Return if product is a Wildcard
     * 
     * @return bool
     */
    public function isWildCard(): bool
    {
        return (bool) $this->is_wildcard;
    }

    /**
     * Return if product is a Multi Domain
     * 
     * @return bool
     */
    public function isMultiDomain(): bool
    {
        return (bool) $this->is_multi_domain;
    }

    /**
     * Return the san max domains of product
     * 
     * @return bool
     */
    public function getSanMaxDomains(): int
    {
        return (int) $this->san_max_domains;
    }

    /**
     * Return the nom domains of product
     * 
     * @return bool
     */
    public function getNumDomains(): int
    {
        return (int) $this->num_domains;
    }
}
