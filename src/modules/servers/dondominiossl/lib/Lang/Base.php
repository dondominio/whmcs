<?php

namespace WHMCS\Module\Server\Dondominiossl\Lang;


abstract class Base implements \WHMCS\Module\Server\Dondominiossl\Lang\Translations
{
    /**
     * Translate a string
     * 
     * @return string
     */
    public function translate(string $toTranslate): string
    {
        $translations = $this->getTranslations();

        return isset($translations[$toTranslate]) ? $translations[$toTranslate] : $toTranslate;
    }
}
