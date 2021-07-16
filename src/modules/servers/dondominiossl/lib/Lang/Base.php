<?php

namespace WHMCS\Module\Server\Dondominiossl\Lang;


abstract class Base implements \WHMCS\Module\Server\Dondominiossl\Lang\Translations
{
    public function translate(string $toTranslate): string
    {
        $translations = $this->getTranslations();

        return isset($translations[$toTranslate]) ? $translations[$toTranslate] : $toTranslate;
    }
}
