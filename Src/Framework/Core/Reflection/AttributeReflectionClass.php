<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 19:29
 */

namespace Famoser\phpSLWrapper\Framework\Core\Reflection;


use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;

class AttributeReflectionClass extends \ReflectionClass
{
    /**
     * @return DatabaseAttributeProperty[]
     */
    function getPropertiesWithDatabaseAttributes()
    {
        $properties = array();
        try {
            $rc = $this;
            do {
                $file = file_get_contents($rc->getFileName());
                $pos = strpos($file, "@database");
                while ($pos > 0) {
                    $file = substr($file, $pos);

                    //get properties of attribute
                    $attrStr = substr($file, 9, strpos($file, "*/") - 9);
                    $attr = explode(",", $attrStr);

                    $type = trim($attr[0]);
                    unset($attr[0]);

                    //clean Array
                    $cleanedArr = array();
                    foreach ($attr as $item) {
                        $trim = trim($item);
                        if ($trim != "")
                            $cleanedArr[] = strtolower($trim);
                    }

                    //evaluate Array
                    $notNull = false;
                    if (($key = array_search("notnull", $cleanedArr)) !== false) {
                        $notNull = true;
                        unset($cleanedArr[$key]);
                    }

                    $autoIncrement = false;
                    if (($key = array_search("autoincrement", $cleanedArr)) !== false) {
                        $autoIncrement = true;
                        unset($cleanedArr[$key]);
                    }

                    $primary = false;
                    if (($key = array_search("primary", $cleanedArr)) !== false) {
                        $primary = true;
                        unset($cleanedArr[$key]);
                    }

                    if (count($cleanedArr) > 0) {
                        Logger::getInstance()->logError("Unknown properties of database attribute", $cleanedArr);
                    }

                    //get name
                    $file = substr($file, strpos($file, "$") + 1);
                    $name = substr($file, 0, strpos($file, ";"));
                    $properties[] = new DatabaseAttributeProperty($name, $type, $notNull, $autoIncrement, $primary);

                    $pos = strpos($file, "@database");
                }
            } while ($rc = $rc->getParentClass());
        } catch (\ReflectionException $e) {
            Logger::getInstance()->logException($e);
        }
        return $properties;
    }

    /**
     * @return \ReflectionProperty[]
     */
    function getPropertiesJsonAttributes()
    {
        $properties = array();
        try {
            $rc = $this;
            do {
                $file = file_get_contents($rc->getFileName());
                $pos = strpos($file, "@json");
                while ($pos > 0) {
                    $file = substr($file, $pos);
                    $file = substr($file, strpos($file, "$") + 1);
                    $name = substr($file, 0, strpos($file, ";"));
                    $pos = strpos($file, "@json");
                    $properties[] = $name;
                }
            } while ($rc = $rc->getParentClass());
        } catch (\ReflectionException $e) {
            Logger::getInstance()->logException($e);
        }
        $reflectProps = $this->getProperties();
        $res = array();
        foreach ($reflectProps as $reflectProp) {
            if (in_array($reflectProp->getName(), $properties))
                $res[] = $reflectProp;
        }
        return $res;
    }
}