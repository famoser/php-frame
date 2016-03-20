<?php

/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 12:23
 */
namespace famoser\phpFrame\Helpers;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Singleton\Singleton;
use famoser\phpFrame\Interfaces\Helpers\IHelper;

class HelperBase extends Singleton implements IHelper
{
    public function evaluateFailure($const)
    {
        LogHelper::getInstance()->logError("evaluateFailure not implemented for " . get_called_class());
        return "";
    }
}