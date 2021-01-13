<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Domains\DomainLookup\ResultsList;

class CheckAvailability extends Action
{
    public function __invoke()
    {
        $resultList = new ResultsList();

        foreach ($this->getParam('tldsToInclude') as $tld) {
            $response = $this->getApp()->getService('api')->checkDomain($this->getParam('searchTerm') . $tld);
            $domainInfo = $response->get("domains")[0];

            $temp = explode('.', $domainInfo['name']);
            $name = $temp[0];

            $searchResult = new SearchResult($name, $domainInfo['tld']);
            $status = $domainInfo['available'] ? SearchResult::STATUS_NOT_REGISTERED : SearchResult::STATUS_REGISTERED;
            $searchResult->setStatus($status);

            $resultList->append($searchResult);
        }

        return $resultList;
    }
}