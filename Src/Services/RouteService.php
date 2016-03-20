<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.02.2016
 * Time: 19:31
 */

namespace famoser\phpFrame\Services;


use famoser\phpFrame\Controllers\ControllerBase;
use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Models\Services\ControllerModel;
use famoser\phpFrame\Models\View\IconMenuItem;

class RouteService extends ServiceBase
{
    private $controllers = array();
    private $menus = array();

    public function __construct()
    {
        parent::__construct();

        $routes = SettingsService::getInstance()->getValueFor("Routes");
        foreach ($routes as $route) {
            $this->controllers[$route["Controller"]] = new ControllerModel($route["Url"], $route["Controller"]);
        }

        $menuInfo = $this->getConfig("Menus");
        foreach ($menuInfo as $menu) {
            foreach ($menu["Entries"] as $item) {
                if (isset($this->getControllers()[$item["Controller"]]))
                    $this->menus[$menu["Name"]] = new IconMenuItem($item["Name"], $this->controllers[$item["Controller"]]->getUrl(), $item["Icon"]);
                else
                    LogHelper::getInstance()->logError("unknown controller used in menu: " . $item["Controller"]);
            }
        }
    }

    /**
     * @return ControllerModel[]
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * @param string $url
     * @return ControllerModel|false
     */
    public function getController($url)
    {
        $favController = null;
        foreach ($this->getControllers() as $controller) {
            if (strlen($url) > $controller->getUrl() && str_starts_with($url, $controller->getUrl())) {
                if ($favController == null || strlen($favController->getUrl()) < strlen($controller->getUrl()))
                    $favController = $controller;
            }
        }
        return $favController;
    }

    public function getMenu($key)
    {
        if (isset($this->menus[$key])) {
            return $this->menus[$key];
        } else {
            LogHelper::getInstance()->logError("Unknown menu: " . $key);
            return null;
        }
    }

    public function getAbsoluteLink($relative)
    {
        $starts = array("http", "www");
        if (str_starts_with_any($relative, $starts))
            return $relative;
        return RuntimeService::getInstance()->getRouteUrl() . "/" . $relative;
    }
}