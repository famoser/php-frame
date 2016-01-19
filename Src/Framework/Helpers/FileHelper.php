<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 14:10
 */
namespace Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper;
use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;

/**
 * @param $path
 * @return string Path
 */
function ensure_dir_exists($path)
{
    if (!is_dir($path))
        mkdir($path);
    return $path;
}

function path_by_namespace($class)
{
    // project-specific namespace prefix
    $prefix = 'Famoser\\phpSLWrapper\\';
    $basedir = null;
    $relative_class = null;

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $basedir = SPL_BASE_DIR;
    }

    /*
    /prefix for Helpers (not classes)
    $prefix = 'Famoser\\phpSLWrapper\\Framework\\Helpers\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $relative_class = substr($relative_class, 0, strpos($class,"\\"));
        $basedir = SPL_BASE_DIR;
    }
    */


    if ($basedir != null) {
        // get the relative class name
        return $basedir . "/" . str_replace('\\', '/', $relative_class) . '.php';

    } else {
        Logger::getInstance()->logFatal("invalid namespace prefix! class: " . $class);
    }
    return "";
}