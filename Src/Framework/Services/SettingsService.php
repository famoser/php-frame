<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 11:50
 */

namespace Famoser\phpSLWrapper\Framework\Services;


use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;
use Famoser\phpSLWrapper\Framework\Core\Singleton\Singleton;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\assert_all_keys_exist;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\ensure_dir_exists;

class SettingsService extends Singleton
{
    const SOURCE_DIR = 10000;
    const HELPER_DIR = 10001;
    const DATA_DIR = 10002;
    const TEMP_DIR = 10003;


    const DIRECTORY_SEPARATOR = 10010;


    const BUILD_TYPE_DEBUG = 10100;
    const BUILD_TYPE_TEST = 10101;
    const BUILD_TYPE_RELEASE = 10102;

    private $config;
    private $buildType;

    public function __construct()
    {
        $configFilePath = $this->getValueFor(SettingsService::SOURCE_DIR) . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "configuration.json";
        if (file_exists($configFilePath)) {
            $configJson = file_get_contents($configFilePath);
            if (strlen($configJson) > 0) {
                $this->config = json_decode($configJson, true);
                $this->configure();
            } else {
                Logger::getInstance()->logFatal("configuration file is empty at " . $configFilePath);
            }
        } else {
            Logger::getInstance()->logFatal("could not find configuration file at " . $configFilePath);
        }
    }

    private function configure()
    {
        $keys = array("Build");
        if (!assert_all_keys_exist($this->config, $keys)) {
            Logger::getInstance()->logAssertFailed("Not all keys are defined", $keys);
        } else {
            $buildKeys = array("Type");
            if (!assert_all_keys_exist($this->config["Build"], $buildKeys)) {
                Logger::getInstance()->logAssertFailed("Not all keys inside the Build key are defined", $keys);
            } else {
                $this->toBuildType($this->config["Build"]["Type"]);
            }
        }
    }

    private function toBuildType($value)
    {
        if ($value == "Debug")
            $this->buildType = SettingsService::BUILD_TYPE_DEBUG;
        else if ($value == "Test")
            $this->buildType = SettingsService::BUILD_TYPE_TEST;
        else if ($value == "Release")
            $this->buildType = SettingsService::BUILD_TYPE_RELEASE;
        else {
            Logger::getInstance()->logFatal("Unknown build type inside the configuration file. Allowed are only Release, Test and Debug");
        }
    }

    /**
     * @param $const string key from configuration file, or enum from SettingService::ENUM
     * @return array|string returns array for key, and string for SettingService::ENUM
     */
    public function getValueFor($const)
    {
        if (is_numeric($const)) {
            if ($const >= SettingsService::SOURCE_DIR && $const <= SettingsService::TEMP_DIR)
                return $this->getDirConst($const);
            else if ($const >= SettingsService::DIRECTORY_SEPARATOR && $const <= SettingsService::DIRECTORY_SEPARATOR)
                return $this->getPhpConst($const);
            else {
                Logger::getInstance()->logFatal("Unknown Setting: " . $const);
                return "";
            }
        } else {
            return $this->getConfiguration($const);
        }
    }

    public function getBuildType()
    {
        return $this->buildType;
    }

    private function getDirConst($const)
    {
        if ($const == SettingsService::SOURCE_DIR) {
            return dirname(dirname(__DIR__));
        } else if ($const == SettingsService::HELPER_DIR) {
            return $this->getValueFor(SettingsService::SOURCE_DIR) . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "Framework" . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "Helpers";
        } else if ($const == SettingsService::DATA_DIR) {
            return ensure_dir_exists(dirname($this->getValueFor(SettingsService::SOURCE_DIR)) . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "Instance" . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "Data");
        } else if ($const == SettingsService::TEMP_DIR) {
            return ensure_dir_exists(dirname($this->getValueFor(SettingsService::SOURCE_DIR)) . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "Instance" . $this->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . "Temp");
        } else {
            Logger::getInstance()->logFatal("Unknown Dir const: " . $const);
            return "";
        }
    }

    private function getPhpConst($const)
    {
        if ($const == SettingsService::DIRECTORY_SEPARATOR) {
            return DIRECTORY_SEPARATOR;
        } else {
            Logger::getInstance()->logFatal("Unknown PHP const: " . $const);
            return "";
        }
    }

    private function getConfiguration($key)
    {
        if (isset($this->config[$key]))
            return $this->config[$key];
        else {
            Logger::getInstance()->logFatal("Unknown Config key: " . $key);
            return array();
        }
    }
}