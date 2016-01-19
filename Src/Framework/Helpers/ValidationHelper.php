<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 00:45
 */

namespace Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper;

use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;

/**
 * @param $path
 * @return bool if file exists
 */
function assert_file_exist($path)
{
    if (file_exists($path))
        return true;
    Logger::getInstance()->logAssertFailed("file does not exist: " . $path);
    return false;
}

/**
 * @param array $path
 * @return bool if all files exist
 */
function assert_files_exist(array $path)
{
    $val = true;
    foreach ($path as $item) {
        $val = $val && assert_file_exist($item);
    }
    Logger::getInstance()->logAssert("all files exist", $val);
    return $val;
}

/**
 * @param array $arr
 * @param array $keys
 * @param bool $doLog if logAssertFailed should be called
 * @return bool if all keys are defined in array
 */
function assert_all_keys_exist(array $arr, array $keys, bool $doLog = false)
{
    if ($arr == null)
        return false;
    if ($keys == null)
        return false;

    $var = true;
    foreach ($keys as $key) {
        if (!isset($arr[$key])) {
            $var = false;
            Logger::getInstance()->logAssertFailed("key does not exist in array: " . $key, $arr);
        }
    }
    return $var;
}