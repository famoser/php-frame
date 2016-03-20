<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 15:10
 */

namespace famoser\phpFrame\Models\View;


class IconMenuItem extends MenuItem
{
    private $icon;

    public function __construct($name, $href, $icon)
    {
        parent::__construct($name, $href);
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }
}