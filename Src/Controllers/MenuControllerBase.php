<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 11.02.2016
 * Time: 17:45
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Models\View\IconMenuItem;
use famoser\phpFrame\Models\View\MenuItem;
use famoser\phpFrame\Services\RouteService;
use famoser\phpFrame\Services\SettingsService;
use famoser\phpFrame\Views\ViewBase;

abstract class MenuControllerBase extends ControllerBase
{
    private $subMenu = array();
    private $mainMenu = array();

    public function __construct($request, $params, $files)
    {
        parent::__construct($request, $params, $files);
        $this->mainMenu = RouteService::getInstance()->getMenu("MainMenu");
    }

    /**
     * @param MenuItem $menuItem
     */
    public function addMenuItem(MenuItem $menuItem)
    {
        $this->subMenu[] = $menuItem;
    }

    protected function returnView(ViewBase $view)
    {
        $view->setMenus($this->mainMenu, $this->subMenu);
        return $view->loadTemplate();
    }
}