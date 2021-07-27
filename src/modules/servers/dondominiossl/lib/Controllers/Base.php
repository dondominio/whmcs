<?php

namespace WHMCS\Module\Server\Dondominiossl\Controllers;


abstract class Base
{
    const VIEW_INDEX = 'index';

    protected ?\WHMCS\Module\Server\Dondominiossl\App $app = null;
    protected ?\WHMCS\Module\Addon\Dondominio\Helpers\Response $response = null;
    protected ?\WHMCS\Module\Addon\Dondominio\Helpers\Request $request = null;
    protected string $errorMsg = '';

    public function __construct(\WHMCS\Module\Server\Dondominiossl\App $app)
    {
        $this->app = $app;
    }

    abstract protected function getViews(): array;

    public function process(): array
    {
        $customAction = $this->getRequest()->getParam('custom_action', static::VIEW_INDEX);
        $views = $this->getViews();
        $response = [];

        if (isset($views[$customAction]) && method_exists($this, $views[$customAction])) {
            $function = $views[$customAction];
            $response = $this->$function();
        }

        return is_array($response) ? $response : [];
    }

    protected function send(string $templateFile, array $variables = []): array
    {
        $variables['js'] = implode(DIRECTORY_SEPARATOR, [dirname(dirname(__DIR__)), 'templates/js.tpl']);
        $variables['error_msg'] = $this->errorMsg;
        $variables['DD_LANG'] = $this->getApp()->getLanguage()->getTranslations();

        return [
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => $variables,
        ];
    }

    protected function setErrorMsg(string $errorMsg): void
    {
        $this->errorMsg = $errorMsg;
    }

    /**
     * Returns Request instance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Request
     */
    public function getRequest(): \WHMCS\Module\Addon\Dondominio\Helpers\Request
    {
        return \WHMCS\Module\Addon\Dondominio\Helpers\Request::getInstance();
    }

    /**
     * Returns Response instance
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Response
     */
    public function getResponse(): \WHMCS\Module\Addon\Dondominio\Helpers\Response
    {
        return \WHMCS\Module\Addon\Dondominio\Helpers\Response::getInstance();
    }

    /**
     * Generate a url of the client area
     * 
     * @return string
     */
    protected function buildUrl(string $view, array $extraParams = []): string
    {
        $serviceID = $this->getRequest()->getParam('id', '');
        $url = sprintf('clientarea.php?action=productdetails&id=%s&custom_action=%s', $serviceID, $view);

        foreach ($extraParams as $key => $param){
            $url .= sprintf('&%s=%s', $key, $param);
        }

        return $url;
    }

    /**
     * Get a instance of App
     * 
     * @return \WHMCS\Module\Server\Dondominiossl\App
     */
    protected function getApp(): \WHMCS\Module\Server\Dondominiossl\App
    {
        if (is_null($this->app)) {
            $this->app = new \WHMCS\Module\Server\Dondominiossl\App();
        }

        return $this->app;
    }

    /**
     * Translate a string with a implementation of WHMCS\Module\Server\Dondominiossl\Lang\Translations
     * 
     * @return string
     */
    public function translate(string $toTranslate): string
    {
        $language = $this->getApp()->getLanguage();
        return $language->translate($toTranslate);
    }
}
