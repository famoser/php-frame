<?php
/**
 * Created by PhpStorm.
 * User: florianmoser
 * Date: 08.03.16
 * Time: 19:57
 */

namespace famoser\phpFrame\WorkFlows;


use famoser\phpFrame\Core\Singleton\Singleton;

abstract class WorkFlowBase extends Singleton
{
    public function __construct()
    {
    }

    abstract function execute();
}