<?php

namespace WHMCS\Module\Addon\Dondominio\Controllers\Admin;

use WHMCS\Module\Addon\Dondominio\Controllers\Controller as BaseController;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class Controller extends BaseController
{
    const VIEW_INDEX = '';

    protected $actualView = null;

    /**
     * Gets available actions for Controller
     * 
     * @return array
     */
    public static function getActions()
    {
        return [
            static::VIEW_INDEX => 'view_Index'
        ];
    }

    /**
     * Searchs and returns a template
     * 
     * @param string $view View in format "folder.file" or "file"
     * @param array $params Params to pass to template
     * 
     * @return \WHMCS\Module\Addon\Dondominio\Helpers\Template
     */
    public function view($view, array $params = [])
    {
        $params['css_path'] = sprintf('/modules/addons/%s/css/', $this->getApp()->getName());
        $params['version'] =  $this->getApp()->getVersion();

        if(!isset($params['print_nav'])){
            $params['print_nav'] = true;
        }

        if(!isset($params['print_title'])){
            $params['print_title'] = true;
        }
        
        return parent::view($view, $params);
    }

    /**
     * Set the pagination params
     * 
     * @param array $params View parameters
     * @param int $limit Limit of pagination
     * @param int $page Actual page
     * @param int $totalRecords Total results
     * 
     * @return void
     */
    protected function setPagination(&$params, $limit, $page, $totalRecords)
    {
        $total_pages = ceil($totalRecords / $limit);
        $paginationSelect = [];

        $total_pages = (int) $total_pages === 0 ? 1 : $total_pages;
        $page = $page > $total_pages ? $total_pages : $page;

        for ($i = 1; $i <= $total_pages; $i++) {
            $paginationSelect[$i] = $i;
        }

        $params['pagination'] = [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalRecords,
            'total_pages' => $total_pages
        ];
        $params['pagination_select'] = $paginationSelect;
    }

    /**
     * Set the actual page for nav
     * 
     * @param string $view View  const
     * 
     * @return void
     */
    protected function setActualView($view)
    {
        $this->actualView = $view;
    }

    /**
     * check the actual page for nav
     * 
     * @param string $view View  const
     * 
     * @return void
     */
    protected function checkActualView($view)
    {
        return $this->actualView === $view;
    }
}

