<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 19:47
 */

namespace Famoser\phpSLWrapper\Framework\Core\Reflection;


use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;
use Famoser\phpSLWrapper\Framework\Services\DataService;
use JsonSerializable;

class DatabaseAttributeProperty implements JsonSerializable
{
    private $name;
    private $type;
    private $dataType;
    private $notNull;
    private $autoIncrement;
    private $primary;

    const DATA_TYPE_UNKNOWN = 0;
    const DATA_TYPE_INT = 1;
    const DATA_TYPE_DATETIME = 2;
    const DATA_TYPE_DATE = 3;
    const DATA_TYPE_TIME = 4;
    const DATA_TYPE_STRING = 5;
    const DATA_TYPE_DOUBLE = 6;

    public function __construct($name, $type, $notNull = false, $autoIncrement = false, $primary = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->notNull = $notNull;
        $this->autoIncrement = $autoIncrement;
        $this->primary = $primary;

        $compareType = strtolower($this->type);
        if ($compareType == "datetime") {
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_DATETIME;
        } else if ($compareType == "date") {
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_DATE;
        } else if ($compareType == "time") {
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_TIME;
        } else if ($compareType == "int") {
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_INT;
        } else if ($compareType == "text" || strpos($compareType, "varchar") !== false) {
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_STRING;
        } else if ($compareType == "decimal" || $compareType == "float" || $compareType == "double" || $compareType == "real") {
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_DOUBLE;
        } else {
            Logger::getInstance()->logWarning("Unknown Data Type in Attribute " . $name . ": " . $type);
            $this->dataType = DatabaseAttributeProperty::DATA_TYPE_UNKNOWN;
        }
    }

    public function getAsCreateSql($flavour = DataService::DRIVER_MYSQL)
    {
        $sql = $this->name . " " . $this->type;
        if ($this->notNull)
            $sql .= "  NOT NULL";
        if ($this->autoIncrement) {
            if ($flavour == DataService::DRIVER_SQLITE)
                $sql .= " AUTOINCREMENT";
            else if ($flavour == DataService::DRIVER_MYSQL)
                $sql .= " AUTO_INCREMENT";
            else {
                Logger::getInstance()->logError("Unknown Database Driver: " . $flavour . ". Using AUTOINCREMENT.");
                $sql .= " AUTOINCREMENT";
            }
        }
        return $sql;
    }

    public function isPrimary()
    {
        return $this->primary;
    }

    public function getAsPrimaryKeySql()
    {
        return "PRIMARY KEY (" . $this->name . ")";
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function convertPropertyValueForDatabase($oldValue)
    {
        if ($this->dataType == DatabaseAttributeProperty::DATA_TYPE_DATETIME) {
            if ($oldValue == null || $oldValue == "")
                return null;
            else
                return date(DataService::FORMAT_DATETIME, strtotime($oldValue));
        }

        if ($this->dataType == DatabaseAttributeProperty::DATA_TYPE_DATE) {
            if ($oldValue == null || $oldValue == "")
                return null;
            else
                $oldValue = date(DataService::FORMAT_DATE, strtotime($oldValue));
        }

        if ($this->dataType == DatabaseAttributeProperty::DATA_TYPE_TIME) {
            if ($oldValue == null || $oldValue == "")
                return null;
            else
                return date(DataService::FORMAT_TIME, strtotime($oldValue));
        }
        return $oldValue;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'Name' => $this->name,
            'Type' => $this->type,
            'NotNull' => $this->notNull,
            'AutoIncrement' => $this->autoIncrement,
            'Primary' => $this->primary
        ];
    }
}