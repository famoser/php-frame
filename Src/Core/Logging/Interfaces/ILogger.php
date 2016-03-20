<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 10.02.2016
 * Time: 11:22
 */

namespace famoser\phpFrame\Core\Logging\Interfaces;


use famoser\phpFrame\Core\Logging\LogItem;

interface ILogger
{
    public function addLogItem(LogItem $item);
    public function getLogItems($clearAfter = true);
}