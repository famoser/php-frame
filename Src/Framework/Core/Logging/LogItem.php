<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 22:33
 */

namespace Famoser\phpSLWrapper\Framework\Core\Logging;


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

    function renderAsText()
    {
        return $this->getLogLevelAsString() . ": " . $this->message . "\n" . $this->source;
    }

    function renderAsHtml()
    {
        return "<b>".$this->getLogLevelAsString() . "</b>: " . $this->message . "<br/>" . $this->source;
    }

    private function getLogLevelAsString()
    {
        if ($this->logLevel == Logger::LOG_LEVEL_INFO) {
            return "info";
        } else if ($this->logLevel == Logger::LOG_LEVEL_WARNING) {
            return "warning";
        } else if ($this->logLevel == Logger::LOG_LEVEL_ERROR) {
            return "error";
        } else if ($this->logLevel == Logger::LOG_LEVEL_FATAL) {
            return "fatal";
        } else if ($this->logLevel == Logger::LOG_LEVEL_EXCEPTION) {
            return "exception occured";
        } else if ($this->logLevel == Logger::LOG_LEVEL_ASSERT_FAILED) {
            return "assert failed";
        } else if ($this->logLevel == Logger::LOG_LEVEL_ASSERT_VALIDATED) {
            return "assert validated";
        }
        return "logtype unknown";
    }

}