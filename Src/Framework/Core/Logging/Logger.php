<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 22:37
 */

namespace Famoser\phpSLWrapper\Framework\Core\Logging;


use Exception;
use Famoser\phpSLWrapper\Framework\Core\Singleton\Singleton;

class Logger extends Singleton
{
    const LOG_LEVEL_DEBUG = 0;
    const LOG_LEVEL_INFO = 1;
    const LOG_LEVEL_WARNING = 2;
    const LOG_LEVEL_ERROR = 3;
    const LOG_LEVEL_FATAL = 4;
    const LOG_LEVEL_EXCEPTION = 4;
    const LOG_LEVEL_ASSERT_FAILED = 10;
    const LOG_LEVEL_ASSERT_VALIDATED = 10;

    private $logItems = array();

    public function __construct()
    {

    }


    /**
     * log debug message
     * @param $message
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logDebug($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_DEBUG, $message, $object);
    }

    /**
     * log an informational message
     * @param $message
     */
    public function logInfo($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_INFO, $message, $object);
    }

    /**
     * log a message which may need the attention of the developer, but may not endager the main purpose of the application
     * @param $message
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logWarning($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_WARNING, $message, $object);
    }


    /**
     * log an error which occurred while executing a task and may be related to incorrect data / misconfiguration
     * @param $message
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logError($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_ERROR, $message, $object);
    }

    /**
     * log an error which may be related to a programming mistake, and not related to corrupt data
     * @param $message
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logFatal($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_FATAL, $message, $object);
    }

    /**
     * log an exception
     * @param $exception
     */
    public function logException(Exception $exception, $message = null)
    {
        $msg = $exception->getMessage();
        if ($message != null) {
            $msg = "Message: " . $message . " Exception: " . $msg;
        }
        $this->doLog(Logger::LOG_LEVEL_FATAL, $msg, null);
    }


    /**
     * log an Assert (the result of a functionality validation test) as failed
     * @param $message
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logAssertFailed($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_ASSERT_FAILED, $message, $object);
    }

    /**
     * log an Assert (the result of a functionality validation test) as validated
     * @param $message
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logAssertValidated($message, $object = null)
    {
        $this->doLog(Logger::LOG_LEVEL_ASSERT_VALIDATED, $message, $object);
    }

    /**
     * log an Assert (the result of a functionality validation test)
     * @param $message
     * @param bool $outcome result of validation
     * @param null $object optional: reference an object important to understand the log entry
     */
    public function logAssert($message, bool $outcome, $object = null)
    {
        if ($outcome === true)
            $this->logAssertValidated($message, $object);
        else
            $this->logAssertFailed($message, $object = null);
    }

    /**
     * @return LogItem[]
     */
    public function getLogItems()
    {
        return $this->logItems;
    }

    /**
     * @return int
     */
    public function countLogItems()
    {
        return count($this->logItems);
    }

    /**
     * @return string as HTML formatted logs
     */
    public function getLogsAsHtml()
    {
        $str = "";
        foreach ($this->getLogItems() as $logItem) {
            $str .= "<p>" . $logItem->renderAsHtml() . "</p>";
        }
        return $str;
    }

    /**
     * @return string as string with \n line divisors formatted logs
     */
    public function getLogsAsText()
    {
        $str = "";
        foreach ($this->getLogItems() as $logItem) {
            $str .= $logItem->renderAsText() . "\n\n";
        }
        return $str;
    }

    private function doLog($level, $message, $object)
    {
        $source = $this->getSource();

        $addMessage = $this->getObjectInfo($object);
        if ($addMessage != "")
            $addMessage = " passed object: " . $addMessage;

        $logItem = new LogItem($source, $level, $message . $addMessage);
        $this->addLogItem($logItem);
    }

    private function getObjectInfo($object)
    {
        if (is_null($object))
            return "";
        if (is_object($object) || is_array($object))
            return json_encode($object);
        return $object;
    }

    private function addLogItem(LogItem $log)
    {
        $this->logItems[] = $log;
    }

    private function getSource($skips = 3)
    {
        $callstack = "";
        foreach (debug_backtrace() as $item) {
            $args = array();
            foreach ($item["args"] as $arg) {
                if ($arg instanceof Exception)
                    $args[] = $arg->getMessage();
                else
                    $args[] = $arg;
            }
            if ($skips-- <= 0) {
                $callstack .= "at " . $item["function"] . ", line " . $item["line"] . " in file " . $item["file"] . " with args " . json_encode($args) . LogItem::LineDivisor;
            }
        }

        return $callstack;
    }
}