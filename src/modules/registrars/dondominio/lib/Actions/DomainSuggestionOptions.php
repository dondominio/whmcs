<?php

namespace WHMCS\Module\Registrar\Dondominio\Actions;

class DomainSuggestionOptions extends Action
{
    public function __invoke()
    {
        return [
            'language' => [
                'FriendlyName' => 'Language',
                'Type' => 'dropdown',
                'Options' => [
                    'en' => 'English',
                    'es' => 'Spanish',
                    'zh' => 'Chinese',
                    'fr' => 'French',
                    'de' => 'German',
                    'kr' => 'Korean',
                    'pt' => 'Portuguese',
                    'tr' => 'Turkish'
                ],
                'Description' => 'Language for Domain Suggestions',
            ],
        ];
    }
}