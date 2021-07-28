<?php

namespace WHMCS\Module\Addon\Dondominio\Helpers;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Dondominio\App;
use WHMCS\Module\Addon\Dondominio\Models\Pricing_Model;
use WHMCS\Module\Addon\Dondominio\Models\Settings_Model;
use WHMCS\Module\Addon\Dondominio\Models\TldSettings_Model;
use WHMCS\Module\Addon\Dondominio\Models\Watchlist_Model;
use Exception;

class Migrations
{
    /**
     * Creates database schema for a fresh install
     *
     * @throws \Exception if some query fails
     *
     * @return void
     */
    public static function install()
    {
        try {
            // mod_dondominio_pricing

            if (!Capsule::schema()->hasTable(Pricing_Model::getTableName())) {
                Capsule::schema()->create(Pricing_Model::getTableName(), function ($table) {
                    $table->integer('id', true)->autoIncrement();
                    $table->string('tld', 64);
                    $table->decimal('register_price', 10, 2)->nullable();
                    $table->decimal('transfer_price', 10, 2)->nullable();
                    $table->decimal('renew_price', 10, 2)->nullable();
                    $table->string('register_range', 128)->nullable();
                    $table->string('transfer_range', 128)->nullable();
                    $table->string('renew_range', 128)->nullable();
                    $table->decimal('old_register_price', 10, 2)->nullable();
                    $table->decimal('old_transfer_price', 10, 2)->nullable();
                    $table->decimal('old_renew_price', 10, 2)->nullable();
                    $table->tinyInteger('authcode_required')->nullable();
                    $table->dateTime('last_update');
                });
            }

            // mod_dondominio_tld_settings

            if (!Capsule::schema()->hasTable(TldSettings_Model::getTableName())) {
                Capsule::schema()->create(TldSettings_Model::getTableName(), function($table) {
                    $table->integer('id', true)->autoIncrement();
                    $table->string('tld', 64)->unique('unique_tld');
                    $table->tinyInteger('ignore');
                    $table->tinyInteger('enabled');
                    $table->decimal('register_increase', 10, 2)->default(0);
                    $table->string('register_increase_type', 16)->default('fixed');
                    $table->decimal('renew_increase', 10, 2)->default(0);
                    $table->string('renew_increase_type', 16)->default('fixed');
                    $table->decimal('transfer_increase', 10, 2)->default(0);
                    $table->string('transfer_increase_type', 16)->default('fixed');
                });
            }

            // mod_dondominio_settings

            if (!Capsule::schema()->hasTable(Settings_Model::getTableName())) {
                Capsule::schema()->create(Settings_Model::getTableName(), function($table) {
                    $table->string('key', 32)->primary();
                    $table->string('value', 255)->nullable();
                });
            }

            // mod_dondominio_watchlist

            if (!Capsule::schema()->hasTable(Watchlist_Model::getTableName())) {
                Capsule::schema()->create(Watchlist_Model::getTableName(), function ($table) {
                    $table->integer('id', true)->autoIncrement();
                    $table->string('tld', 64);
                });
            }

            // Insert default values

            Capsule::beginTransaction();

            Capsule::table(Settings_Model::getTableName())->insert([
                ['key' => 'register_increase', 'value' => '0.00'],
                ['key' => 'transfer_increase', 'value' => '0.00'],
                ['key' => 'renew_increase', 'value' => '0.00'],
                ['key' => 'register_increase_type', 'value' => 'fixed'],
                ['key' => 'transfer_increase_type', 'value' => 'fixed'],
                ['key' => 'renew_increase_type', 'value' => 'fixed'],
                ['key' => 'notifications_enabled', 'value' => '0'],
                ['key' => 'notifications_email', 'value' => ''],
                ['key' => 'notifications_new_tlds', 'value' => '0'],
                ['key' => 'notifications_prices', 'value' => '0'],
                ['key' => 'api_username', 'value' => ''],
                ['key' => 'api_password', 'value' => ''],
                ['key' => 'watchlist_mode', 'value' => 'disable'],
                ['key' => 'prices_autoupdate', 'value' => '0'],
                ['key' => 'api_conexion', 'value' => '1'],
                ['key' => 'last_version', 'value' => ''],
                ['key' => 'last_version_ts_update', 'value' => '0000-00-00 00:00:00'],
            ]);

            Capsule::commit();
        } catch (Exception $e) {
            logModuleCall(App::NAME, __FUNCTION__, '', $e->getMessage());

            Capsule::rollback();

            Capsule::schema()->dropIfExists(Pricing_Model::getTableName());
            Capsule::schema()->dropIfExists(TldSettings_Model::getTableName());
            Capsule::schema()->dropIfExists(Settings_Model::getTableName());
            Capsule::schema()->dropIfexists(Watchlist_Model::getTableName());
        }
    }

    /**
     * Deletes database schema for the application
     *
     * @throws \Exception if some query fails
     *
     * @return void
     */
    public static function uninstall()
    {
        try {
            Capsule::schema()->dropIfExists(Pricing_Model::getTableName());
            Capsule::schema()->dropIfExists(TldSettings_Model::getTableName());
            Capsule::schema()->dropIfExists(Settings_Model::getTableName());
            Capsule::schema()->dropIfexists(Watchlist_Model::getTableName());
        } catch (Exception $e) {
            logModuleCall(App::NAME, __FUNCTION__, '', $e->getMessage());

            throw $e;
        }
    }

    /**
     * Upgrades database schema for the application
     *
     * @return void
     */
    public static function upgrade($version)
    {
        try {
            if (version_compare($version, '1.1', '<')) {
                static::upgrade11();
            }
        
            if (version_compare($version, '1.2', '<')) {
                static::upgrade12();
            }
        
            if (version_compare($version, '1.6', '<')) {
                static::upgrade16();
            }

            if (version_compare($version, '2.0', '<')) {
                static::upgrade20();
            }

            if (version_compare($version, '2.1.1', '<')) {
                static::upgrade211();
            }
            if (version_compare($version, '2.1.2', '<')) {
                static::upgrade212();
            }
            if (version_compare($version, '2.1.3', '<')) {
                static::upgrade213();
            }
            if (version_compare($version, '2.2.0', '<')) {
                static::upgrade220();
            }
        } catch (Exception $e) {
            logModuleCall(App::NAME, __FUNCTION__, '', $e->getMessage());

            throw $e;
        }
    }

    /**
     * Upgrades database schema for version 1.1
     *
     * In version 1.1 we introduce `authcode_require` field in `mod_dondominio_pricing`
     *
     * @return void
     */
    protected static function upgrade11()
    {
        if (!Capsule::schema()->hasColumn('mod_dondominio_pricing', 'authcode_required')) {
            Capsule::schema()->table('mod_dondominio_pricing', function($table) {
                $table->tinyInteger('authcode_required')->nullable();
            });
        }
    }

    /**
     * Upgrades database schema for version 1.2
     *
     * In version 1.2 we introduce the new table `mod_dondominio_tld_settings`
     *
     * @return void
     */
    protected static function upgrade12()
    {
        if (!Capsule::schema()->hasTable('mod_dondominio_tld_settings')) {
            Capsule::schema()->create('mod_dondominio_tld_settings', function($table) {
                $table->integer('id', true)->autoIncrement();
                $table->string('tld', 64)->unique('unique_tld');
                $table->tinyInteger('ignore');
                $table->tinyInteger('enabled');
                $table->decimal('register_increase', 10, 2)->default(0);
                $table->string('register_increase_type', 16)->default('fixed');
                $table->decimal('renew_increase', 10, 2)->default(0);
                $table->string('renew_increase_type', 16)->default('fixed');
                $table->decimal('transfer_increase', 10, 2)->default(0);
                $table->string('transfer_increase_type', 16)->default('fixed');
            });
        }
    }

    /**
     * Upgrades database schema for version 1.6
     *
     * In version 1.6 we introduce the new field `ignore` in table `mod_dondominio_tld_settings`
     *
     * @return void
     */
    protected static function upgrade16()
    {
        if (!Capsule::schema()->hasColumn('mod_dondominio_tld_settings', 'ignore')) {
            Capsule::schema()->table('mod_dondominio_tld_settings', function($table) {
                $table->tinyInteger('ignore');
            });
        }
    }

     /**
     * Upgrades database schema for version 2.0
     *
     * In version 2.0, suggests domains changed from addon to registrar
     *
     * @return void
     */
    protected static function upgrade20()
    {
        Settings_Model::where('key', 'suggests_enabled')->delete();
        Settings_Model::where('key', 'suggests_language')->delete();
        Settings_Model::where('key', 'suggests_tlds')->delete();

        // Fix collations

        $pdo = Capsule::connection()->getPdo();
        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `register_range` `register_range` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `transfer_range` `transfer_range` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `renew_range` `renew_range` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_settings` CHANGE `key` `key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_settings` CHANGE `value` `value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `register_increase_type` `register_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `renew_increase_type` `renew_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `transfer_increase_type` `transfer_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_watchlist` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
    }

     /**
     * Upgrades database schema for version 2.1.1
     *
     * In version 2.1.1, we need to revert not nullable fields
     *
     * @return void
     */
    protected static function upgrade211()
    {
        $pdo = Capsule::connection()->getPdo();

        // Fix not nulls

        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_settings` CHANGE `key` `key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `register_increase_type` `register_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `renew_increase_type` `renew_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `transfer_increase_type` `transfer_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_watchlist` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
    }

     /**
     * Upgrades database schema for version 2.1.2
     *
     * In version 2.1.2, fix VARCHAR(256) to VARCHAR(255)
     *
     * @return void
     */
    protected static function upgrade212()
    {
        $pdo = Capsule::connection()->getPdo();

        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `register_range` `register_range` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `transfer_range` `transfer_range` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `renew_range` `renew_range` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_settings` CHANGE `value` `value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');

        $pdo->query('ALTER TABLE `mod_dondominio_pricing` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_settings` CHANGE `key` `key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `register_increase_type` `register_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `renew_increase_type` `renew_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_tld_settings` CHANGE `transfer_increase_type` `transfer_increase_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        $pdo->query('ALTER TABLE `mod_dondominio_watchlist` CHANGE `tld` `tld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
    }

    /**
     * Upgrades database schema for version 2.1.3
     *
     * Save API Conexion in DB
     *
     * @return void
     */
    protected static function upgrade213()
    {
        Capsule::table('mod_dondominio_settings')->updateOrInsert(['key' => 'api_conexion'], ['value' => 1]);
        Capsule::table('mod_dondominio_settings')->updateOrInsert(['key' => 'last_version'], ['value' => '']);
        Capsule::table('mod_dondominio_settings')->updateOrInsert(['key' => 'last_version_ts_update'], ['value' => '0000-00-00 00:00:00']);
    }

    /**
     * Upgrades database schema for version 2.2.0
     *
     * Tables for SSL Module
     *
     * @return void
     */
    protected static function upgrade220()
    {
        if (!Capsule::schema()->hasTable('mod_dondominio_ssl_products')) {

            $priceIncrementsType = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::getPriceIncrementTypes();
            $priceIncrementsTypeNone = \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::PRICE_INCREMENT_TYPE_NONE;

            Capsule::schema()->create('mod_dondominio_ssl_products', function($table) use ($priceIncrementsType, $priceIncrementsTypeNone) {
                $table->integer('dd_product_id');
                $table->primary('dd_product_id');
                $table->integer('tblproducts_id')->default(0);
                $table->string('product_name', 255)->default('');
                $table->string('brand_name', 255)->default('');
                $table->string('validation_type', 255)->default('');
                $table->tinyInteger('is_multi_domain')->default(0);
                $table->tinyInteger('is_wildcard')->default(0);
                $table->tinyInteger('is_trial')->default(0);
                $table->integer('num_domains')->default(1);
                $table->integer('key_length')->default(0);
                $table->string('encryption', 255)->default('');
                $table->decimal('price_create')->default(0);
                $table->decimal('price_renew')->default(0);
                $table->integer('trial_period')->default(0);
                $table->integer('san_max_domains')->default(0);
                $table->decimal('san_price')->default(0);
                $table->decimal('price_create_increment')->default(0);
                $table->enum('price_create_increment_type', $priceIncrementsType)->default($priceIncrementsTypeNone);
                $table->tinyInteger('available')->default(1);
            });
        }

        if (!Capsule::schema()->hasTable('mod_dondominio_ssl_certificate_orders')) {
            Capsule::schema()->create('mod_dondominio_ssl_certificate_orders', function($table) {
                $table->integer('certificate_id');
                $table->primary('certificate_id');
                $table->integer('tblhosting_id')->default(0);
                $table->integer('dd_product_id')->default(0);
                $table->tinyInteger('renew_date_flag')->default(0);
            });
        }
    }
}