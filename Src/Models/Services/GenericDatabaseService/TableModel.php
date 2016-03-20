<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 18:45
 */

namespace famoser\phpFrame\Models\Services\GenericDatabaseService;


use famoser\phpFrame\Core\Tracing\TraceHelper;
use famoser\phpFrame\Core\Tracing\TraceInstance;
use famoser\phpFrame\Services\GenericDatabaseService;

class TableModel implements \JsonSerializable
{
    const ERROR_TABLE_NAME_NOT_SET = 1;
    const ERROR_OBJECT_NAME_NOT_SET = 2;
    const ERROR_PROPERTIES_NO_ARRAY = 3;

    private $tableName;
    private $objectName;
    private $properties;

    const TABLE_PROPERTY_CONST_ADD = 10000;

    public function setConfig($config, $canSkipName = false)
    {
        if (isset($config["TableName"]))
            $this->tableName = $config["TableName"];
        else {
            if (!$canSkipName)
                return TableModel::ERROR_TABLE_NAME_NOT_SET;
        }

        if (isset($config["ObjectName"]))
            $this->objectName = $config["ObjectName"];
        else {
            return TableModel::ERROR_OBJECT_NAME_NOT_SET;
        }

        if (isset($config["Properties"])) {
            if (is_array($config["Properties"])) {
                $this->properties = array();
                foreach ($config["Properties"] as $item) {
                    $prop = new InputPropertyModel();
                    $res = $prop->setConfig($item);
                    if ($res === true)
                        $this->properties[$item["Name"]] = $prop;
                    else {
                        return $res + TableModel::TABLE_PROPERTY_CONST_ADD;
                    }
                }
            } else {
                return TableModel::ERROR_PROPERTIES_NO_ARRAY;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @return InputPropertyModel[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param InputPropertyModel[] $props
     */
    public function addProperties(array $props)
    {
        $this->properties = array_merge($props, $this->properties);
    }

    public function getInstance()
    {
        return new $this->objectName;
    }

    public static function evaluateError($error)
    {
        if ($error > TableModel::TABLE_PROPERTY_CONST_ADD) {
            $error -= TableModel::TABLE_PROPERTY_CONST_ADD;
            return TablePropertyModel::evaluateError($error);
        } else if ($error == TableModel::ERROR_OBJECT_NAME_NOT_SET)
            return "object name not set";
        else if ($error == TableModel::ERROR_PROPERTIES_NO_ARRAY)
            return "properties are no array";
        else if ($error == TableModel::ERROR_TABLE_NAME_NOT_SET)
            return "table name not set";
        return "unknown error";
    }

    public function getPreparedValues($driver, array $values, $removeNull = false, array $ignore = null)
    {
        $res = array();
        foreach ($values as $key => $value) {
            if ((!is_array($ignore) || !in_array($key, $ignore)) && isset($this->getProperties()[$key])) {
                if (!$removeNull || $value != null) {
                    $prop = $this->getProperties()[$key];
                    $val = $prop->convertToDatabaseValue($driver, $value);
                    if ($val != null || !$removeNull)
                        $res[$prop->getDatabaseName()] = $val;
                }
            }
        }
        return $res;
    }

    public function getPreparedValuesFromObject($driver, $obj, $removeNull = false, array $ignore = null)
    {
        $res = array();
        foreach ($this->getProperties() as $property) {
            if (!is_array($ignore) || !in_array($property->getDatabaseName(), $ignore)) {
                $value = $property->getValueFromObject($obj);
                if (!$removeNull || $value != null) {
                    $val = $property->convertToDatabaseValue($driver, $value);
                    if ($val != null || !$removeNull)
                        $res[$property->getDatabaseName()] = $val;
                }
            }
        }
        return $res;
    }

    public function testModel(TraceInstance $trace)
    {
        $instance = $this->getInstance();
        if ($instance == null) {
            $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "cannot create instance of " . $this->objectName);
            return false;
        }
        $successful = true;
        foreach ($this->getProperties() as $property) {
            $successful &= $property->assertObjectProperties($instance, $trace);
        }
        return $successful;
    }

    public function getCreateTableSql($driverType, $tableName = null)
    {
        if ($tableName == null)
            $tableName = $this->getTableName();
        $sql = "CREATE TABLE " . $tableName . " (";
        $propSql = array();
        foreach ($this->getProperties() as $property) {
            if ($property->getType() != TablePropertyModel::TYPE_1N_RELATION)
                $propSql[] = $property->getCreateTableSql($driverType);
        }
        return $sql . implode(",", $propSql) . ")";
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