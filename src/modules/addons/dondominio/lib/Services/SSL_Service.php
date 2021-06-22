<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\SSLService_Interface;
use WHMCS\Database\Capsule;

class SSL_Service extends AbstractService implements SSLService_Interface
{

    public function apiSync(int $page = 0): void
    {
        $apiService = $this->getApp()->getService('api');
        $productResponse = $apiService->getSSLProductList($page);
        $products = $productResponse->get("products");

        foreach ($products as $product) {
            $this->createProduct($product);
        }

        $limit = $productResponse->get('queryInfo')['pageLength'];
        $results = $productResponse->get('queryInfo')['results'];

        if ($results === $limit) {
            $this->apiSync($page++);
        }
    }

    public function getProduct(int $id): \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model
    {
        return \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::where(['dd_product_id' => $id])->first();
    }

    public function getProductGroups(): array
    {
        $groups = [];

        foreach (\WHMCS\Product\Group::cursor() as $group){
            $groups[$group->id] = $group->name;
        }

        return $groups;
    }

    public function createProduct(array $args): void
    {
        $insert = [];

        $this->setIfExists($insert, 'dd_product_id', $args, 'productID');
        $this->setIfExists($insert, 'product_name', $args, 'productName');
        $this->setIfExists($insert, 'brand_name', $args, 'brandName');
        $this->setIfExists($insert, 'validation_type', $args, 'validationType');
        $this->setIfExists($insert, 'is_multi_domain', $args, 'isMultiDomain');
        $this->setIfExists($insert, 'is_wildcard', $args, 'isWildcard');
        $this->setIfExists($insert, 'is_trial', $args, 'isTrial');
        $this->setIfExists($insert, 'num_domains', $args, 'numDomains');
        $this->setIfExists($insert, 'min_years', $args, 'minYears');
        $this->setIfExists($insert, 'max_years', $args, 'maxYears');
        $this->setIfExists($insert, 'key_length', $args, 'keyLength');
        $this->setIfExists($insert, 'encryption', $args, 'encryption');
        $this->setIfExists($insert, 'price_create', $args, 'priceCreate');
        $this->setIfExists($insert, 'price_renew', $args, 'priceRenew');
        $this->setIfExists($insert, 'trial_period', $args, 'trialPeriod');
        
        $insert['status'] = $this->validProduct($args['productID']);

        Capsule::table('mod_dondominio_ssl_products')->updateOrInsert(['dd_product_id' => $args['productID']], $insert);
    }

    protected function setIfExists(array &$insert, string $insertKey, array $response, string $responseKey): void
    {
        if (isset($response[$responseKey])){
            $insert[$insertKey] = $response[$responseKey];
        }
    }

    protected function validProduct(int $productID): int
    {
        return true;
        $validProducts = [55, 56];

        return (int) in_array($productID, $validProducts);
    }
}
