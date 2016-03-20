<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 18:47
 */

namespace famoser\phpFrame\Models\Services\GenericDatabaseService;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Tracing\TraceHelper;
use famoser\phpFrame\Core\Tracing\TraceInstance;
use famoser\phpFrame\Helpers\FormatHelper;
use famoser\phpFrame\Services\GenericDatabaseService;

class TablePropertyModel implements \JsonSerializable
{
    const ERROR_NAME_UNDEFINED = 1;
    const ERROR_TYPE_UNDEFINED = 2;
    const ERROR_TYPE_UNKNOWN = 3;
    const ERROR_AUTO_INCREMENT_MUST_BE_BOOLEAN = 4;
    const ERROR_PRIMARY_KEY_MUST_BE_BOOLEAN = 5;
    const ERROR_TARGET_OBJECT_NAME_MUST_BE_DEFINED = 6;
    const ERROR_TARGET_PROPERTY_NAME_MUST_BE_DEFINED = 7;

    const TYPE_TEXT = 10;
    const TYPE_INTEGER = 11;
    const TYPE_DOUBLE = 12;
    const TYPE_BOOLEAN = 13;
    const TYPE_DATE = 14;
    const TYPE_DATETIME = 15;
    const TYPE_TIME = 16;
    const TYPE_N1_RELATION = 17;
    const TYPE_1N_RELATION = 18;

    private $name;
    private $type;
    private $autoIncrement = false;
    private $primaryKey = false;
    private $targetObjectName;
    private $targetPropertyName;

    public function setConfig($config)
    {
        if (isset($config["Name"]))
            $this->name = $config["Name"];
        else {
            return TablePropertyModel::ERROR_NAME_UNDEFINED;
        }

        if (isset($config["AutoIncrement"])) {
            if ($config["AutoIncrement"] === true)
                $this->autoIncrement = true;
            else if ($config["AutoIncrement"] === false)
                $this->autoIncrement = false;
            else
                return TablePropertyModel::ERROR_AUTO_INCREMENT_MUST_BE_BOOLEAN;
        }

        if (isset($config["PrimaryKey"])) {
            if ($config["PrimaryKey"] == true)
                $this->primaryKey = true;
            else if ($config["PrimaryKey"] == false)
                $this->primaryKey = false;
            else
                return TablePropertyModel::ERROR_PRIMARY_KEY_MUST_BE_BOOLEAN;
        }

        if (isset($config["Type"])) {
            if ($config["Type"] == "text" || $config["Type"] == "string")
                $this->type = TablePropertyModel::TYPE_TEXT;
            else if ($config["Type"] == "double" || $config["Type"] == "float" || $config["Type"] == "decimal")
                $this->type = TablePropertyModel::TYPE_DOUBLE;
            else if ($config["Type"] == "int" || $config["Type"] == "integer" || $config["Type"] == "enum")
                $this->type = TablePropertyModel::TYPE_INTEGER;
            else if ($config["Type"] == "bool" || $config["Type"] == "boolean")
                $this->type = TablePropertyModel::TYPE_BOOLEAN;
            else if ($config["Type"] == "date")
                $this->type = TablePropertyModel::TYPE_DATE;
            else if ($config["Type"] == "datetime")
                $this->type = TablePropertyModel::TYPE_DATETIME;
            else if ($config["Type"] == "time")
                $this->type = TablePropertyModel::TYPE_TIME;
            else if ($config["Type"] == "__n1Relation") {
                $this->type = TablePropertyModel::TYPE_N1_RELATION;
                if (!isset($config["TargetObjectName"]))
                    return TablePropertyModel::ERROR_TARGET_OBJECT_NAME_MUST_BE_DEFINED;
                $this->targetObjectName = $config["TargetObjectName"];
                if (!isset($config["TargetPropertyName"]))
                    return TablePropertyModel::ERROR_TARGET_PROPERTY_NAME_MUST_BE_DEFINED;
                $this->targetPropertyName = $config["TargetPropertyName"];
            } else if ($config["Type"] == "__1nRelation") {
                $this->type = TablePropertyModel::TYPE_1N_RELATION;
                if (!isset($config["TargetObjectName"]))
                    return TablePropertyModel::ERROR_TARGET_OBJECT_NAME_MUST_BE_DEFINED;
                $this->targetObjectName = $config["TargetObjectName"];
            } else
                return TablePropertyModel::ERROR_TYPE_UNKNOWN;
        } else
            return TablePropertyModel::ERROR_TYPE_UNDEFINED;

        return true;
    }

    public static function evaluateError($error)
    {
        if ($error == TablePropertyModel::ERROR_PRIMARY_KEY_MUST_BE_BOOLEAN)
            return "PrimaryKey must be boolean";
        else if ($error == TablePropertyModel::ERROR_AUTO_INCREMENT_MUST_BE_BOOLEAN)
            return "AutoIncrement key must be boolean";
        else if ($error == TablePropertyModel::ERROR_TARGET_PROPERTY_NAME_MUST_BE_DEFINED)
            return "target property name must be defined";
        else if ($error == TablePropertyModel::ERROR_TYPE_UNKNOWN)
            return "unknown type";
        else if ($error == TablePropertyModel::ERROR_NAME_UNDEFINED)
            return "name undefined";
        else if ($error == TablePropertyModel::ERROR_TARGET_OBJECT_NAME_MUST_BE_DEFINED)
            return "target object name must be defined";
        return "unknown error occured";
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    private function getTypeName($driverType)
    {
        if ($driverType == GenericDatabaseService::DRIVER_TYPE_MYSQL) {
            if (TablePropertyModel::TYPE_TEXT == $this->getType())
                return "TEXT";
            else if (TablePropertyModel::TYPE_INTEGER == $this->getType())
                return "INT";
            else if (TablePropertyModel::TYPE_DOUBLE == $this->getType())
                return "DOUBLE";
            else if (TablePropertyModel::TYPE_BOOLEAN == $this->getType())
                return "TINYINT(1)";
            else if (TablePropertyModel::TYPE_DATE == $this->getType())
                return "DATE";
            else if (TablePropertyModel::TYPE_DATETIME == $this->getType())
                return "DATETIME";
            else if (TablePropertyModel::TYPE_TIME == $this->getType())
                return "TIME";
            else if (TablePropertyModel::TYPE_N1_RELATION == $this->getType())
                return "INT";
            return false;
        } else {
            if (TablePropertyModel::TYPE_TEXT == $this->getType())
                return "TEXT";
            else if (TablePropertyModel::TYPE_INTEGER == $this->getType())
                return "INTEGER";
            else if (TablePropertyModel::TYPE_DOUBLE == $this->getType())
                return "REAL";
            else if (TablePropertyModel::TYPE_BOOLEAN == $this->getType())
                return "INTEGER";
            else if (TablePropertyModel::TYPE_DATE == $this->getType())
                return "TEXT";
            else if (TablePropertyModel::TYPE_DATETIME == $this->getType())
                return "TEXT";
            else if (TablePropertyModel::TYPE_TIME == $this->getType())
                return "TEXT";
            else if (TablePropertyModel::TYPE_N1_RELATION == $this->getType())
                return "INTEGER";
            return false;
        }
    }

    public function getCreateTableSql($driverType)
    {
        if ($driverType == GenericDatabaseService::DRIVER_TYPE_MYSQL) {
            $sql = $this->name . " " . $this->getTypeName($driverType);
            if ($this->autoIncrement === true) {
                $sql .= " auto_increment";
            }
            if ($this->primaryKey === true) {
                $sql .= ",PRIMARY KEY (" . $this->name . ")";
            }
            return $sql;
        } else if ($driverType == GenericDatabaseService::DRIVER_TYPE_SQLITE) {

            $sql = $this->name . " " . $this->getTypeName($driverType);
            if ($this->autoIncrement === true) {
                $sql .= " AUTO_INCREMENT";
            }
            if ($this->primaryKey === true) {
                $sql .= " PRIMARY KEY";
            }
            return $sql;
        }
        return false;
    }

    public function getDatabaseName()
    {
        return $this->name;
    }

    public function convertToDatabaseValue($driverType, $value)
    {
        if (TablePropertyModel::TYPE_TEXT == $this->getType())
            return $value;
        else if (TablePropertyModel::TYPE_INTEGER == $this->getType())
            return is_numeric($value) ? $value : null;
        else if (TablePropertyModel::TYPE_DOUBLE == $this->getType())
            return is_numeric($value) ? $value : null;
        else if (TablePropertyModel::TYPE_BOOLEAN == $this->getType()) {
            if (is_bool($value))
                return $value;
            $parsedVal = strtolower($value);
            return $parsedVal == "true" || $value == 1 ? true : false;
        } else if (TablePropertyModel::TYPE_DATE == $this->getType())
            return FormatHelper::getInstance()->dateDatabase($value);
        else if (TablePropertyModel::TYPE_DATETIME == $this->getType())
            return FormatHelper::getInstance()->dateTimeDatabase($value);
        else if (TablePropertyModel::TYPE_TIME == $this->getType())
            return FormatHelper::getInstance()->timeDatabase($value);
        else if (TablePropertyModel::TYPE_N1_RELATION == $this->getType())
            return is_numeric($value) ? $value : 0;
        return null;
    }

    private function getSetMethod()
    {
        return "set" . $this->getDatabaseName();
    }

    private function getGetMethod()
    {
        return "get" . $this->getDatabaseName();
    }

    public function assertObjectProperties($object, TraceInstance $trace)
    {
        if (!method_exists($object, $this->getSetMethod())) {
            $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Method not defined in class " . get_class($object) . ": " . $this->getSetMethod());
            return false;
        }
        if (!method_exists($object, $this->getGetMethod())) {
            $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Method not defined in class " . get_class($object) . ": " . $this->getGetMethod());
            return false;
        }
        return true;
    }

    public function getValueFromObject($object)
    {
        $methodName = $this->getGetMethod();
        return $object->$methodName();
    }

    public function setValueToObject($object, $value)
    {
        $methodName = $this->getSetMethod();
        return $object->$methodName($value);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }
}