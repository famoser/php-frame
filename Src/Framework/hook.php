<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 23:29
 */

namespace Famoser\phpSLWrapper\Framework\Hook;

use Famoser\phpSLWrapper\Framework;
use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\path_by_namespace;
use Famoser\phpSLWrapper\Framework\Services\SettingsService;

function hi_framework()
{
    define("SPL_BASE_DIR", dirname(__DIR__));
    define("HELPER_BASE_DIR", __DIR__ . DIRECTORY_SEPARATOR . "Helpers");
    include_all_files_in_dir(HELPER_BASE_DIR, "php");

    //register autoload
    spl_autoload_extensions('.php');
    spl_autoload_register('spl_autoload_register');
}

spl_autoload_register(function ($class) {

    $path = path_by_namespace($class);

    // if the file exists, require it
    if ($path != null && file_exists($path)) {
        require $path;
    } else {
        // does the class use the Framework namespace prefix?
        $prefix = 'Famoser\\phpSLWrapper\\Framework\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            Logger::getInstance()->logFatal("class not found! class: " . $class . " | path: " . $path);
        }
    }
});

function include_all_files_in_dir($path, $fileEnding)
{
    foreach (glob($path . "/*." . $fileEnding) as $filename) {
        require_once $filename;
    }
}

function bye_framework()
{

}