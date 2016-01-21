<?php
use function Famoser\phpSLWrapper\Framework\Hook\hi_framework;

/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21.01.2016
 * Time: 19:25
 */

function InitTestFramework()
{
    include dirname(__DIR__) . DIRECTORY_SEPARATOR . "Framework" . DIRECTORY_SEPARATOR . "Hook.php";
    hi_framework();
}