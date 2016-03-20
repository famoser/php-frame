<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 10.02.2016
 * Time: 00:36
 */

namespace famoser\phpFrame\Models\Locale;


use famoser\phpFrame\Core\Logging\LogHelper;

class ResourceWrapper
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getKey($key, $throwError = true)
    {
        if (!isset($this->config[$key])) {
            if ($throwError)
                LogHelper::getInstance()->logWarning("Not translated: " . $key);
            return $key;
        }
        return $this->config[$key];
    }
}