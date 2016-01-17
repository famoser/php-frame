<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 22:37
 */

namespace Famoser\phpSLWrapper\Framework\Core\Logging;


use Famoser\phpSLWrapper\Framework\Core\Singleton\Singleton;

class Logger extends Singleton
{
    const LOG_LEVEL_INFO = 1;
    const LOG_LEVEL_WARNING = 2;
    const LOG_LEVEL_ERROR = 3;
    const LOG_LEVEL_FATAL = 4;
    const LOG_LEVEL_EXCEPTION = 4;
    const LOG_LEVEL_ASSERT_FAILED = 10;
    const LOG_LEVEL_ASSERT_VALIDATED = 10;

    private $logItems = array();


    /**
     * log an informational message
     * @param $message
     */
    public function logInfo($message)
    {
        $this->doLog(Logger::LOG_LEVEL_INFO, $message);
    }

    /**
     * log a message which may need the attention of the developer, but may not endager the main purpose of the application
     * @param $message
     */
    public function logWarning($message)
    {
        $this->doLog(Logger::LOG_LEVEL_WARNING, $message);
    }


    /**
     * log an error which occurred while executing a task and may be related to incorrect data / misconfiguration
     * @param $message
     */
    public function logError($message)
    {
        $this->doLog(Logger::LOG_LEVEL_ERROR, $message);
    }

    /**
     * log an error which may be related to a programming mistake, and not related to corrupt data
     * @param $message
     */
    public function logFatal($message)
    {
        $this->doLog(Logger::LOG_LEVEL_FATAL, $message);
    }

    /**
     * log an exception
     * @param $exception
     */
    public function logException(\Exception $exception)
    {
        $this->doLog(Logger::LOG_LEVEL_FATAL, strval($exception));
    }


    /**
     * log an Assert (the result of a functionality validation test) as failed
     * @param $message
     */
    public function logAssertFailed($message)
    {
        $this->doLog(Logger::LOG_LEVEL_ASSERT_FAILED, $message);
    }

    /**
     * log an Assert (the result of a functionality validation test) as validated
     * @param $message
     */
    public function logAssertValidated($message)
    {
        $this->doLog(Logger::LOG_LEVEL_ASSERT_VALIDATED, $message);
    }

    /**
     * log an Assert (the result of a functionality validation test)
     * @param $message
     * @param bool $outcome result of validation
     */
    public function logAssert($message, bool $outcome)
    {
        if ($outcome === true)
            $this->logAssertValidated($message);
        else
            $this->logAssertFailed($message);
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

    public function getLogsAsHtml()
    {
        $str = "";
        foreach ($this->getLogItems() as $logItem) {
            $str .= "<p>".$logItem->renderAsHtml()."</p>";
        }
        return $str;
    }

    public function getLogsAsText()
    {
        $str = "";
        foreach ($this->getLogItems() as $logItem) {
            $str .= $logItem->renderAsText()."\n\n";
        }
        return $str;
    }

    private function doLog($level, $message)
    {
        $source = $this->getSource();
        $logItem = new LogItem($source, $level, $message);
        $this->addLogItem($logItem);
    }

    private function addLogItem(LogItem $log)
    {
        $this->logItems[] = $log;
    }

    private function getSource($skips = 3)
    {
        /* debug_backtrace() is in form like
        [0]=>
          array(4) {
            ["file"] => string(10) "/tmp/a.php"
            ["line"] => int(10)
            ["function"] => string(6) "a_test"
            ["args"]=>
            array(1) {
              [0] => &string(6) "friend"
            }

        skip first 2
        */

        $callstack = "";
        foreach (debug_backtrace() as $item) {
            if ($skips-- <= 0) {
                $callstack .= "at " . $item["function"] . ", line " . $item["line"] . " in file " . $item["file"] . " with args " . implode(",", $item["args"]) . "\n";
            }
        }

        return $callstack;
    }
}