<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 17:49
 */

namespace famoser\phpFrame\Models\Locale;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\FileHelper;

class Language
{
    private $config;
    private $folder;
    private $name;

    private $resources = array();
    private $resourcesLoaded = false;
    private $formats = array();
    private $formatsLoaded = false;

    public function __construct($name, $config, $folder)
    {
        $this->name = $name;
        $this->config = $config;
        $this->folder = $folder;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ResourceWrapper
     */
    public function getResources()
    {
        if (!$this->resourcesLoaded) {
            $resourceArr = array();
            foreach ($this->config["ResourceFiles"] as $resourceFile) {
                $configFilePath = $this->folder . DIRECTORY_SEPARATOR . $resourceFile;
                $resp = FileHelper::getInstance()->getJsonArray($configFilePath);
                if ($resp === false)
                    LogHelper::getInstance()->logFatal("could not find resource file at " . $configFilePath);
                else
                    $resourceArr = array_merge($resp, $resourceArr);
            }
            $this->resources = new ResourceWrapper($resourceArr);
            $this->resourcesLoaded = true;
        }
        return $this->resources;
    }

    public function getFormats()
    {
        if (!$this->formatsLoaded) {
            foreach ($this->config["FormatFiles"] as $formatFile) {
                $configFilePath = $this->folder . DIRECTORY_SEPARATOR . $formatFile;
                $resp = FileHelper::getInstance()->getJsonArray($configFilePath);
                if ($resp === false)
                    LogHelper::getInstance()->logFatal("could not find format file at " . $configFilePath);
                else
                    $this->formats = array_merge($resp, $this->formats);
            }
            $this->formatsLoaded = true;
        }
        return $this->formats;
    }
}