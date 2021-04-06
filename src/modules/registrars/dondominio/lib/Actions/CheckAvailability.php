<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Domains\DomainLookup\ResultsList;

class CheckAvailability extends Action
{
    public function __invoke()
    {
        $resultList = new ResultsList();

        $premiumEnabled = (bool) $this->getParam('premiumEnabled');

        foreach ($this->getParam('tldsToInclude') as $tld) {
            $response = $this->getApp()->getService('api')->checkDomain($this->getParam('searchTerm') . $tld);
            $domainInfo = $response->get("domains")[0];

            $status = $domainInfo['available'] ? SearchResult::STATUS_NOT_REGISTERED : SearchResult::STATUS_REGISTERED;

            if (!$premiumEnabled && $domainInfo['premium']) {
                $status = SearchResult::STATUS_RESERVED;
            }

            $temp = explode('.', $domainInfo['name']);
            $name = $temp[0];

            $searchResult = new SearchResult($name, $domainInfo['tld']);
            $searchResult->setStatus($status);

            if ($premiumEnabled && $domainInfo['premium']) {
                $searchResult->setPremiumDomain(true);
                $searchResult->setPremiumCostPricing([
                    'register'  => $domainInfo['price'],
                    'renew'  => $domainInfo['price'],
                    'CurrencyCode'  => $domainInfo['currency'],
                ]);
            }

            $resultList->append($searchResult);
        }

        return $resultList;
    }
}
