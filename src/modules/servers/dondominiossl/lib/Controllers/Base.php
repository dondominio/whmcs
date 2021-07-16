<?php

namespace WHMCS\Module\Server\Dondominiossl\Controllers;


abstract class Base
{
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
        
        if (isset($views[$customAction]) && method_exists($this, $views[$customAction])){
            $function = $views[$customAction];
            return $this->$function();
        }

        return [];
    }

    protected function send(string $templateFile, array $variables = []): array
    {
        $variables['js'] = implode(DIRECTORY_SEPARATOR, [dirname(dirname(__DIR__)), 'templates/js.tpl']);
        $variables['error_msg'] = $this->errorMsg;
        $variables['DD_LANG'] = $this->app->getLanguage()->getTranslations();

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

    protected function buildUrl(string $view): string
    {
        $serviceID = $this->getRequest()->getParam('id', '');
        return sprintf('clientarea.php?action=productdetails&id=%s&custom_action=%s', $serviceID, $view);
    }

    protected function getApp(): \WHMCS\Module\Server\Dondominiossl\App
    {
        if (is_null($this->app)){
            $this->app = new \WHMCS\Module\Server\Dondominiossl\App();
        }

        return $this->app;
    }
}