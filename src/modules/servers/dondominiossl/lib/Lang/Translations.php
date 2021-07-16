<?php

namespace WHMCS\Module\Server\Dondominiossl\Lang;

interface Translations
{
    public function getTranslations(): array;
    public function translate(string $toTranslate): string;
}

