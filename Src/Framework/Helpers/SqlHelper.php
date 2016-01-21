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
    $endSQL = "";
    foreach ($info->getProperties() as $property) {
        $sql .= $property->getAsCreateSql($flavor) . ",";
        if ($property->isPrimary())
            $endSQL .= $property->getAsPrimaryKeySql() . ",";
    }
    if ($endSQL != "") {
        $sql .= $endSQL;
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
    return "SELECT 1 FROM ".$info->getTableName()." LIMIT 1;";
}