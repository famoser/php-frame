<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 09.12.2015
 * Time: 18:16
 */

function remove_first_entry_in_array(array $arr)
{
    unset($arr[0]);
    return array_values($arr);
}

function str_starts_with($haystack, $needle)
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function str_ends_with($haystack, $needle)
{
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function str_starts_with_any($haystack, array $needles, $returnValue = false)
{
    foreach ($needles as $needle) {
        if (str_starts_with($haystack, $needle))
            if ($returnValue)
                return $needle;
            else
                return true;
    }
    return false;
}

function str_equals_with_any($haystack, array $needles, $returnValue = false)
{
    foreach ($needles as $needle) {
        if ($haystack == $needle)
            if ($returnValue)
                return $needle;
            else
                return true;
    }
    return false;
}

function str_ends_with_any($haystack, array $needles, $returnValue = false)
{
    foreach ($needles as $needle) {
        if (str_ends_with($haystack, $needle))
            if ($returnValue)
                return $needle;
            else
                return true;
    }
    return false;
}

function remove_empty_entries(array $arr)
{
    $res = array();
    $allNummeric = true;
    foreach ($arr as $key => $value) {
        if ($value != "") {
            $res[$key] = $value;
            $allNummeric &= is_numeric($key);
        }
    }
    if ($allNummeric)
        return array_values($res);
    return $res;
}

function get_class_lineage($object)
{
    $class_name = get_class($object);
    $parents = array_values(class_parents($class_name));
    return array_merge(array($class_name), $parents);
}