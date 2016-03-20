<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 29.01.2016
 * Time: 09:16
 */

namespace famoser\phpFrame\Services;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Singleton\Singleton;

class ServiceBase extends Singleton
{
    private $config;

    public function __construct($getConfig = true, $customName = null)
    {
        if ($getConfig) {
            if ($customName == null)
                $customName = get_called_class();
            $this->config = SettingsService::getInstance()->getFrameworkConfig($customName);
        }
    }

    /**
     * @param string|array $key
     * @return array|null|string
     */
    protected function getConfig($key)
    {
        return $this->getConfigIntern($key, true);
    }

    /**
     * @param string|array $key
     * @return array|null|string
     */
    protected function tryGetConfig($key)
    {
        return $this->getConfigIntern($key, false);
    }

    private function getConfigIntern($key, $throwError)
    {
        if (is_array($key)) {
            $activeConfig = $this->config;
            foreach ($key as $item) {
                if (isset($activeConfig[$item]))
                    $activeConfig = $activeConfig[$item];
                else {
                    if ($throwError)
                        LogHelper::getInstance()->logError("Unknown key " . $item . " inside " . json_encode($key));
                    return null;
                }
            }
            return $activeConfig;
        } else {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            }
            if ($throwError)
                LogHelper::getInstance()->logError("Unknown Setting: " . $key);
            return null;
        }
    }

    protected function setConfig($config)
    {
        $this->config = $config;
    }
}