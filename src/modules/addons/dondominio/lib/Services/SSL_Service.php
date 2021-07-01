<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\SSLService_Interface;
use WHMCS\Database\Capsule;

class SSL_Service extends AbstractService implements SSLService_Interface
{

    public function apiSync(bool $updatePrices = false, int $page = 0): void
    {
        $apiService = $this->getApp()->getService('api');
        $productResponse = $apiService->getSSLProductList($page);
        $products = $productResponse->get("products");

        foreach ($products as $product) {
            $this->createProduct($product);
            $productObj = $this->getProduct($product['productID']);

            if (is_object($productObj) && $updatePrices) {
                $productObj->updateWhmcsProductPrice();
            }
        }

        $limit = $productResponse->get('queryInfo')['pageLength'];
        $results = $productResponse->get('queryInfo')['results'];

        if ($results === $limit) {
            $this->apiSync($updatePrices, $page++);
        }
    }

    public function getProduct(int $id): \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model
    {
        return \WHMCS\Module\Addon\Dondominio\Models\SSLProduct_Model::where(['dd_product_id' => $id])->first();
    }

    public function getProductGroups(): array
    {
        $groups = [];

        foreach (\WHMCS\Product\Group::cursor() as $group) {
            $groups[$group->id] = $group->name;
        }

        return $groups;
    }

    public function createProduct(array $args): void
    {
        $insert = [];

        $this->setIfExists($insert, 'dd_product_id', $args, ['productID']);
        $this->setIfExists($insert, 'product_name', $args, ['productName']);
        $this->setIfExists($insert, 'brand_name', $args, ['brandName']);
        $this->setIfExists($insert, 'validation_type', $args, ['validationType']);
        $this->setIfExists($insert, 'is_multi_domain', $args, ['isMultiDomain']);
        $this->setIfExists($insert, 'is_wildcard', $args, ['isWildcard']);
        $this->setIfExists($insert, 'is_trial', $args, ['isTrial']);
        $this->setIfExists($insert, 'num_domains', $args, ['numDomains']);
        $this->setIfExists($insert, 'key_length', $args, ['keyLength']);
        $this->setIfExists($insert, 'encryption', $args, ['encryption']);
        $this->setIfExists($insert, 'price_create', $args, ['create', 'create']);
        $this->setIfExists($insert, 'price_renew', $args, ['renew', 'create']);
        $this->setIfExists($insert, 'trial_period', $args, ['trialPeriod']);

        $insert['status'] = $this->validProduct($args['productID']);

        Capsule::table('mod_dondominio_ssl_products')->updateOrInsert(['dd_product_id' => $args['productID']], $insert);
    }

    protected function setIfExists(array &$insert, string $insertKey, array $response, array $responseKeys): void
    {
        foreach ($responseKeys as $value) {
            if (!isset($response[$value])) {
                return;
            }

            $response = $response[$value];
        }

        $insert[$insertKey] = $response;
    }

    protected function validProduct(int $productID): int
    {
        return true;
        $validProducts = [55, 56];

        return (int) in_array($productID, $validProducts);
    }

    public function getCertificateOrder(int $certificateID)
    {
        $order = \WHMCS\Module\Addon\Dondominio\Models\SSLCertificateOrder_Model::where([
            'certificate_id' => $certificateID
        ])->first();

        if (is_object($order)){
            return $order;
        }

        return null;
    }
}
