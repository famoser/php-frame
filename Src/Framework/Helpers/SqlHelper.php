<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 20.01.2016
 * Time: 15:36
 */
namespace Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper;

use Famoser\phpSLWrapper\Framework\Models\DataService\TableInfo;
use Famoser\phpSLWrapper\Framework\Services\DataService;

function createTableSql(TableInfo $info, $flavor = DataService::DRIVER_MYSQL)
{
    $sql = "CREATE TABLE " . $info->getTableName() . " (";
    foreach ($info->getProperties() as $property) {
        $sql .= $property->getAsCreateSql($flavor) . ",";
    }
    $sql = substr($sql, 0, -1) . ")";
    return $sql;
}

function createUpdateSql(TableInfo $info, $flavor = DataService::DRIVER_MYSQL)
{
    $sql = "UPDATE " . $info->getTableName() . " SET ";
    foreach ($info->getProperties() as $property) {
        $sql .= $property->getName() . "=:" . $property->getName() . ",";
    }
    $sql = substr($sql, 0, -1) . " WHERE Id=:Id";
    return $sql;
}

function createInsertSql(TableInfo $info, $flavor = DataService::DRIVER_MYSQL)
{
    $sql = "INSERT INTO " . $info->getTableName() . " (";
    foreach ($info->getProperties() as $property) {
        $sql .= $property->getName() . ",";
    }
    $sql = substr($sql, 0, -1) . ") VALUES (";
    foreach ($info->getProperties() as $property) {
        $sql .= ":" . $property->getName() . ",";
    }
    $sql = substr($sql, 0, -1) . ")";
    return $sql;
}

function createDeleteSql(TableInfo $info, $flavor = DataService::DRIVER_MYSQL)
{
    $sql = "DELETE FROM " . $info->getTableName() . " WHERE Id = :Id";
    return $sql;
}

function createTableExistSql(TableInfo $info, $flavor = DataService::DRIVER_MYSQL)
{
    return "SELECT 1 FROM " . $info->getTableName() . " LIMIT 1;";
}

/**
 * @param TableInfo $info
 * @param array|null $condition : Array of form: array("property"=>"value","property"=>array("value1,value2,value3")). Pass null if all entries of table should be returned
 * @param array|null $orderBy : Array of Form: array("Order1","Order2","Order3"). Pass null if you do not want to order.
 * @param array|null $properties : Array of Form: array("Property1","Property2"). Pass null if you want all properties.
 * @param int $limit : If you want a limit pass number between >=0. Pass value smaller than 0 (I recommend -1 for easy read) to retrieve all results
 * @param int $flavor
 * @return string
 */
function createGetSql(TableInfo $info, array $condition = null, array $orderBy = null, array $properties = null, int $limit = -1, $flavor = DataService::DRIVER_MYSQL)
{
    $sql = "SELECT ";
    if ($properties == null) {
        $sql .= "*";
    } else {
        if (count($properties) > 0) {
            $sql .= implode(",", $properties);
        } else {
            $sql .= "1";
        }
    }
    $sql .= " FROM " . $info->getTableName() . " ";
    if ($condition != null && count($condition) > 0) {
        foreach ($condition as $property => $value) {
            $sql .= $property . " ";
            if (is_array($value)) {
                $sql .= "IN(";
                for ($i = 0; $i < count($value); $i++) {
                    $sql .= $i . $properties . ",";
                }
                $sql = substr($sql, 0, -1) . ")";
            } else {
                $sql .= "=:" . $property;
            }
            $sql .= " AND ";
        }
        $sql = substr($sql, 0, -5);
    }
    if ($orderBy != null && count($orderBy) > 0) {
        $sql .= " ORDER BY " . implode(",", $orderBy);
    }
    if ($limit >= 0) {
        $sql .= " LIMIT " . $limit;
    }

    return $sql;
}