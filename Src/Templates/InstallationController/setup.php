<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 16:45
 */

use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Services\RouteService;
use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) {
    $model = $this->tryRetrieve("model");
    echo PartHelper::getInstance()->getFormStart();
    echo PartHelper::getInstance()->getText("initialize the application by simply clicking submit");
    echo PartHelper::getInstance()->getFormEnd();
}