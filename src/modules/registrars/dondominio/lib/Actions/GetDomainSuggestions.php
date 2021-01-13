<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Domains\DomainLookup\ResultsList;

class GetDomainSuggestions extends Action
{
    public function __invoke()
    {
        $query = $this->getParam('searchTerm');
        $tlds = $this->getParam('tldsToInclude');
        $suggestionSettings = $this->getParam('suggestionSettings');

        $language = (is_array($suggestionSettings) && array_key_exists('language', $suggestionSettings)) ? $suggestionSettings['language'] : '';

        $response = $this->getApp()->getService('api')->getDomainSuggestions($query, $language, $tlds);
        $suggestions = $response->get('suggests');

        $return = new ResultsList();

        foreach ($suggestions as $sld => $tlds) {
            foreach ($tlds as $tld => $available) {
                $searchResult = new SearchResult($sld, $tld);
                $status = $available ? SearchResult::STATUS_NOT_REGISTERED : SearchResult::STATUS_REGISTERED;
                $searchResult->setStatus($status);
                $return->append($searchResult);
            }
        }

        return $return;
    }
}