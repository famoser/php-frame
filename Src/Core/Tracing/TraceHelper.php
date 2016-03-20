<?php
/**
 * Created by PhpStorm.
 * User: florianmoser
 * Date: 08.03.16
 * Time: 20:07
 * */

namespace famoser\phpFrame\Core\Tracing;

use famoser\phpFrame\Core\Singleton\Singleton;

class TraceHelper extends Singleton
{
    private $traceInstances = array();

    const TRACE_LEVEL_INFO = 1;
    const TRACE_LEVEL_WARNING = 2;
    const TRACE_LEVEL_ERROR = 3;
    const TRACE_LEVEL_FAILURE = 4;

    public function getTraceInstance($source)
    {
        $trace = new TraceInstance($source);
        $this->traceInstances[] = $trace;
        return $trace;
    }

    /**
     *
     * @return array[]
     */
    public function getFullTrace($clearAfter = true)
    {
        $res = array();
        foreach ($this->getTraceInstances() as $traceInstance) {
            if (count($traceInstance->getTraces()) > 0) {
                if (!isset($res[$traceInstance->getSource()]))
                    $res[$traceInstance->getSource()] = $traceInstance->getTraces();
                else
                    $res[$traceInstance->getSource()] = array_merge($res[$traceInstance->getSource()], $traceInstance->getTraces());
            }
        }
        if ($clearAfter)
            $this->traceInstances = array();

        return $res;
    }

    public function traceLevelToString($traceLevel)
    {
        if ($traceLevel == TraceHelper::TRACE_LEVEL_INFO)
            return "info";
        else if ($traceLevel == TraceHelper::TRACE_LEVEL_WARNING)
            return "warning";
        else if ($traceLevel == TraceHelper::TRACE_LEVEL_ERROR)
            return "error";
        else if ($traceLevel == TraceHelper::TRACE_LEVEL_FAILURE)
            return "failure";
        return "unknown trace level";
    }

    /**
     * @return TraceInstance[]
     */
    private function getTraceInstances()
    {
        return $this->traceInstances;
    }

}