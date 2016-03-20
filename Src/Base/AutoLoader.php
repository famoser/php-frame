<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 22:30
 */

namespace famoser\phpFrame\Base;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Singleton\Singleton;

class AutoLoader extends Singleton
{
    private $namespaceFolders;

    public function __construct()
    {
        $this->namespaceFolders = array();
    }

    public function addNameSpace($nameSpace, $folder)
    {
        $this->namespaceFolders[$nameSpace] = $folder;
    }

    public function includeClass($class)
    {
        foreach ($this->namespaceFolders as $namespace => $folder) {
            if (strpos($class, $namespace) === 0) {
                $newPath = str_replace($namespace, "", $class);
                $newPath = str_replace("\\", DIRECTORY_SEPARATOR, $newPath);
                $filePath = $folder . DIRECTORY_SEPARATOR . $newPath . ".php";
                if (!file_exists($filePath)) {
                    LogHelper::getInstance()->logFatal("file for class name " . $class . " does not exist at " . $filePath);
                    return false;
                }
                include_once $filePath;
                return true;
            }
        }
        return false;
    }

}