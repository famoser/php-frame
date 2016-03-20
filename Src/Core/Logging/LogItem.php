<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 22:33
 */

namespace famoser\phpFrame\Core\Logging;


class LogItem
{
    private $source;
    private $logLevel;
    private $message;

    public function __construct($source, $logLevel, $message)
    {
        $this->source = $source;
        $this->logLevel = $logLevel;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

}