<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13.02.2016
 * Time: 00:28
 */

namespace famoser\phpFrame\Views;


class GenericCenterView extends GenericView
{
    public function __construct($controller, $view, $folder, $fromFramework)
    {
        parent::__construct($controller, $view, $folder, $fromFramework);
        $this->useCenter(true);
    }

}