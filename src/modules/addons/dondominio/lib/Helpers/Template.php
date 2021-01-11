<?php

namespace WHMCS\Module\Addon\Dondominio\Helpers;

class Template
{
    const BASE_TEMPLATE = 'base.tpl';
    protected $render = null;

    /**
     * Set render
     *
     * @param string $file File
     * @param array $params Parameteres to assign
     */
    public function __construct(string $file, array $params = [])
    {
        $this->render = new \Smarty();
        $this->render->setTemplateDir(implode(DIRECTORY_SEPARATOR, [dirname(dirname(__DIR__)), 'templates']));

        $this->render->assign('CONTENT_FILE', $file);

        foreach ($params as $key => $param) {
            $this->render->assign($key, $param);
        }
    }

    /**
     * Returns renderer
     *
     * @return \Smarty
     */
    public function getRender()
    {
        return $this->render;
    }
}