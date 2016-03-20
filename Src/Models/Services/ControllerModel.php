<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.02.2016
 * Time: 19:35
 */

namespace famoser\phpFrame\Models\Services;


class ControllerModel
{
    private $Url;
    private $Name;
    private $Controller;
    private $Icon;

    public function __construct($url, $controller)
    {
        $this->Url = $url;
        $this->Controller = $controller;
    }

    public function setProperties($name, $icon)
    {
        $this->Name = $name;
        $this->Icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->Url;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->Controller;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->Icon;
    }
}