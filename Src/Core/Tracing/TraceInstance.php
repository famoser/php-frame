<?php
/**
 * Created by PhpStorm.
 * User: florianmoser
 * Date: 08.03.16
 * Time: 20:08
 */

namespace famoser\phpFrame\Core\Tracing;


use famoser\phpFrame\Helpers\FormatHelper;

class TraceInstance
{
    private $source;
    private $highestTraceLevel;
    private $traces = array();

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function trace($state, $content)
    {
        if ($state > $this->highestTraceLevel)
            $this->highestTraceLevel = $state;

        $this->traces[] = FormatHelper::getInstance()->timestamp() . " [" . TraceHelper::getInstance()->traceLevelToString($state) . "] " . $content;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getTraces()
    {
        return $this->traces;
    }

    /**
     * @return mixed
     */
    public function getHighestTraceLevel()
    {
        return $this->highestTraceLevel;
    }
}