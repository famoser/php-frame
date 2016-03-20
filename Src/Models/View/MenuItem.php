<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 15:09
 */

namespace famoser\phpFrame\Models\View;


class MenuItem
{
    private $name;
    private $href;

    public function __construct($name, $href)
    {
        $this->name = $name;
        $this->href = $href;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getHref()
    {
        return $this->href;
    }
}