<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 10.02.2016
 * Time: 11:22
 */

namespace famoser\phpFrame\Core\Logging;


use famoser\phpFrame\Core\Logging\Interfaces\ILogger;

class Logger implements ILogger
{
    private $logItems = array();

    public function addLogItem(LogItem $item)
    {
        $this->logItems[] = $item;
    }

    /**
     * @param bool $clearAfter
     * @return LogItem[]
     */
    public function getLogItems($clearAfter = true)
    {
        if ($clearAfter) {
            $ret = $this->logItems;
            $this->logItems = array();
            return $ret;
        }
        return $this->logItems;
    }
}