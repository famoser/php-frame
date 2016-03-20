<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 23:29
 */


namespace famoser\phpFrame;

use famoser\phpFrame\Base\AutoLoader;
use famoser\phpFrame\Controllers\FrameworkController;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Services\SettingsService;


class phpFrameHook {

    public static function hi_framework() {
        include_once __DIR__ . DIRECTORY_SEPARATOR . "phplibrary.php";
        
        spl_autoload_register(function ($class) {
            if (!AutoLoader::getInstance()->includeClass($class))
                phpFrameHook::bye_framework(false);
        });
        
        $nameSpaces = SettingsService::getInstance()->tryGetValueFor(array("Framework", "AutoLoader"));
        if (is_array($nameSpaces)) {
            foreach ($nameSpaces as $nameSpace => $folder) {
                AutoLoader::getInstance()->addNameSpace($nameSpace, $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $folder);
            }
        }
        
        RuntimeService::getInstance()->setFrameworkDirectory(__DIR__);

        $val = SettingsService::getInstance()->tryGetValueFor(array("Framework", "DebugMode"));
        if ($val === true) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }

    }

    public static function bye_framework($successful = true) {
        if ($successful) {
            exit();
        }

        $controller = new FrameworkController();
        $output = $controller->Display(FrameworkController::SHOW_MESSAGE);
        echo $output;
        exit();
    }
}