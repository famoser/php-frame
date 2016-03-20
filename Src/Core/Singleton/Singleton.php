<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 22:36
 */

namespace famoser\phpFrame\Core\Singleton;

class Singleton implements ISingleton
{
    protected static $instances = array();

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    /**
     * @return static
     */
    final public static function getInstance()
    {
        $classname = get_called_class();
        if (!isset(static::$instances[$classname])) {
            static::$instances[$classname] = new static();
        }

        return static::$instances[$classname];
    }
}

?>