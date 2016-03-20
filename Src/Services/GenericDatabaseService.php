<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 03.07.2015
 * Time: 10:01
 */

namespace famoser\phpFrame\Services;

use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Tracing\TraceHelper;
use famoser\phpFrame\Helpers\FileHelper;
use famoser\phpFrame\Helpers\FormatHelper;
use famoser\phpFrame\Helpers\PasswordHelper;
use famoser\phpFrame\Helpers\ReflectionHelper;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Models\Services\GenericDatabaseService\TableModel;
use famoser\phpFrame\Models\Services\GenericDatabaseService\TablePropertyModel;
use PDO;

class GenericDatabaseService extends DatabaseService
{
    private $tables;

    public function __construct()
    {
        parent::__construct();
        $this->tables = FileHelper::getInstance()->deserializeObject(FileHelper::getInstance()->resolveCachedFile(FileHelper::CACHED_FILE_DATASERVICE_TABLES));
        if ($this->tables == null) {
            $this->tables = array();
        }
    }

    public function setup()
    {
        $objectConfigs = $this->getConfig("Objects");
        $tableConfigs = $this->getConfig("Tables");

        $trace = TraceHelper::getInstance()->getTraceInstance("Database Service");

        /* @var $objects TableModel[] */
        $objects = array();
        foreach ($objectConfigs as $objectConfig) {
            $tableModel = new TableModel();
            $res = $tableModel->setConfig($objectConfig, true);
            if ($res === true) {
                $objects[$objectConfig["ObjectName"]] = $tableModel;
            } else {
                $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Error in " . $objectConfig["ObjectName"] . ": " . TableModel::evaluateError($res));
                return false;
            }
        }

        $this->tables = array();
        foreach ($tableConfigs as $tableConfig) {
            $tableModel = new TableModel();
            $res = $tableModel->setConfig($tableConfig);
            if ($res === true) {
                $this->tables[$tableConfig["ObjectName"]] = $tableModel;
            } else {
                $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Error in " . $tableConfig["ObjectName"] . ": " . TableModel::evaluateError($res));
                return false;
            }
        }

        //inheritance!
        foreach ($this->getTables() as $table) {
            $inst = $table->getInstance();
            $classes = ReflectionHelper::getInstance()->getInheritanceTree($inst);
            unset($classes[0]);
            foreach ($classes as $class) {
                if (isset($objects[$class])) {
                    $table->addProperties($objects[$class]->getProperties());
                } else {
                    $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Base Class not found: " . $class . " for object of type " . $table->getObjectName());
                    return false;
                }
            }
        }

        //assert models are correctly configured
        $successful = true;
        foreach ($this->getTables() as $table) {
            $successful &= $table->testModel($trace);
        }
        if (!$successful) {
            $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "not all objects could setup correctly.");
            return false;
        }

        //create tables, recover data from existing table, drop table, recreate, and fill again
        foreach ($this->getTables() as $table) {
            //get data from old table;
            $sql = "SELECT * FROM " . $table->getTableName();
            $stmt = $this->executeSql($sql, null, true, true);
            $oldContent = null;
            if ($stmt !== false) {
                $oldContent = $this->fetchAllToArray($stmt);
            }

            //drop table
            if (!$this->dropTableInternal($table->getTableName(), true)) {
                if (is_array($oldContent)) {
                    $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Cannot drop table " . $table->getTableName());
                    continue;
                }
            }

            //create new table
            $sql = $table->getCreateTableSql($this->getDatabaseDriver(), $table->getTableName());
            if (!$this->executeSql($sql)) {
                $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "Cannot create table " . $table->getTableName());
                continue;
            }

            //fill new table if old values exist
            if (is_array($oldContent)) {
                foreach ($oldContent as $entry) {
                    $values = $table->getPreparedValues($this->getDatabaseDriver(), $entry);
                    if (!$this->insertInternal($table->getTableName(), $values))
                        $trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "could not insert values: " . json_encode($values));
                }
            }
        }

        FileHelper::getInstance()->cacheFile(FileHelper::CACHED_FILE_DATASERVICE_TABLES, FileHelper::getInstance()->serializeObject($this->getTables()));
        return true;
    }

    /**
     * @return TableModel[]
     */
    private function getTables()
    {
        return $this->tables;
    }

    /**
     * @param BaseDatabaseModel $model
     * @param $id
     * @param boolean $addRelationships
     * @return BaseDatabaseModel
     */
    public function getById(BaseDatabaseModel $model, $id, $addRelationships = true)
    {
        return $this->getSingle($model, array("Id" => $id), $addRelationships);
    }

    /**
     * @param BaseDatabaseModel $model
     * @param null $condition
     * @param boolean $addRelationships
     * @param null $orderBy
     * @param null $additionalSql
     * @return \famoser\phpFrame\Models\Database\BaseDatabaseModel[]
     */
    public function getAll(BaseDatabaseModel $model, $condition = null, $addRelationships = true, $orderBy = null, $additionalSql = null)
    {
        $table = $this->getTableName($model);
        if ($table != null) {
            if ($orderBy != null)
                $orderBy = " ORDER BY " . $orderBy;

            return $this->getAllWithQuery($model, 'SELECT * FROM ' . $table->getTableName() . " " . $this->constructConditionSQL($condition) . $orderBy . " " . $additionalSql, $condition, $addRelationships);
        }
        return array();
    }

    /**
     * @param BaseDatabaseModel $model
     * @param string $sql
     * @param array|null $preparedArray
     * @param boolean $addRelationships
     * @return \famoser\phpFrame\Models\Database\BaseDatabaseModel[]
     */
    protected function getAllWithQuery(BaseDatabaseModel $model, string $sql, array $preparedArray = null, $addRelationships = true)
    {
        $stmt = $this->executeSql($sql, $preparedArray, true);
        if ($stmt !== false)
            return $this->fetchAllToClass($stmt, $model, $addRelationships);
        return null;
    }

    /**
     * @param BaseDatabaseModel $model
     * @param null $condition
     * @param boolean $addRelationships
     * @param string $orderBy
     * @return BaseDatabaseModel
     */
    public function getSingle(BaseDatabaseModel $model, $condition = null, $addRelationships = true, $orderBy = "")
    {
        $table = $this->getTableName($model);
        if ($table != null) {
            if ($orderBy != "")
                $orderBy = " ORDER BY " . $orderBy;

            return $this->getSingleWithQuery($model, 'SELECT * FROM ' . $table->getTableName() . $this->constructConditionSQL($condition) . $orderBy . " LIMIT 1", $condition, $addRelationships);
        }
        return false;
    }

    /**
     * @param BaseDatabaseModel $model
     * @param string $sql
     * @param array|null $preparedArray
     * @param boolean $addRelationships
     * @return BaseDatabaseModel
     */
    protected function getSingleWithQuery(BaseDatabaseModel $model, string $sql, $preparedArray = null, $addRelationships = true)
    {
        $stmt = $this->executeSql($sql . " LIMIT 1", $preparedArray, true);
        if ($stmt !== false)
            return $this->fetchSingleToClass($stmt, $model, $addRelationships);
        return null;
    }

    /**
     * @param \PDOStatement $stmt
     * @param BaseDatabaseModel $model
     * @param boolean $addRelationships
     * @return BaseDatabaseModel[]
     */
    private function fetchAllToClass(\PDOStatement $stmt, BaseDatabaseModel $model, $addRelationships = false)
    {
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, ReflectionHelper::getInstance()->getClassName($model));
        if ($addRelationships)
            foreach ($result as $item) {
                $this->addRelationsToSingle($item);
            }
        return $result;
    }

    /**
     * @param \PDOStatement $stmt
     * @return array[]
     */
    private function fetchAllToArray(\PDOStatement $stmt)
    {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param \PDOStatement $stmt
     * @param BaseDatabaseModel $model
     * @param boolean $addRelationships
     * @return BaseDatabaseModel
     */
    private function fetchSingleToClass(\PDOStatement $stmt, BaseDatabaseModel $model, $addRelationships = false)
    {
        $result = $this->fetchAllToClass($stmt, $model, false);
        if (isset($result[0])) {
            if ($addRelationships)
                $this->addRelationsToSingle($result[0]);
            return $result[0];
        } else
            return false;
    }

    /**
     * @param \PDOStatement $stmt
     * @return array
     */
    private function fetchSingleToArray(\PDOStatement $stmt)
    {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) > 0)
            return $res[0];
        return false;
    }

    public function getPropertyByCondition(BaseDatabaseModel $model, $property, $condition = null, $orderBy = null)
    {
        $table = $this->getTableName($model);

        if ($table != null) {
            if ($orderBy != null)
                $orderBy = " ORDER BY " . $orderBy;
            else
                $orderBy = " ORDER BY " . $property;

            $sql = 'SELECT ' . $property . ' FROM ' . $table->getTableName() . $this->constructConditionSQL($condition) . $orderBy;
            $stmt = $this->executeSql($sql, $condition, true);
            if ($stmt != false) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC, $model);
                $resArray = array();
                foreach ($result as $res) {
                    $resArray[] = $res[$property];
                }

                return $resArray;
            }
        }
        return array();
    }

    public function create(BaseDatabaseModel $model)
    {
        $table = $this->getTableName($model);
        if ($table != null) {
            $arr = $table->getPreparedValuesFromObject(parent::getDatabaseDriver(), $model);
            $arr["ChangeDateTime"] = FormatHelper::getInstance()->dateTimeFromString("now");
            $arr["CreateDateTime"] = FormatHelper::getInstance()->dateTimeFromString("now");
            $arr["ChangedById"] = AuthenticationService::getInstance()->getUser()->getId();
            $arr["CreatedById"] = AuthenticationService::getInstance()->getUser()->getId();

            $arr = $this->cleanUpGenericArray($arr);
            if (isset($arr["Id"])) {
                unset($arr["Id"]);
            }
            $resp = $this->insertInternal($table, $arr);
            if ($resp !== false) {
                $model->setId($resp);
                return true;
            }
        }
        return false;
    }

    public function update(BaseDatabaseModel $model, array $allowedArr = null)
    {
        $arr = $model->getDatabaseArray();
        $arr["ChangeDateTime"] = FormatHelper::getInstance()->dateTimeFromString("now");
        $arr["ChangedById"] = AuthenticationService::getInstance()->getUser()->getId();

        $table = $this->getTableName($model);

        $arr = $this->cleanUpGenericArray($arr);
        $params = $arr;
        if (is_array($allowedArr)) {
            $params = array();
            foreach ($allowedArr as $item) {
                $params[$item] = $arr[$item];
            }
        }
        if (!isset($arr["Id"]) || $arr["Id"] == 0) {
            return false;
        } else {
            return $this->updateInternal($table, $params);
        }
    }

    public function delete(BaseDatabaseModel $model)
    {
        if ($model->getId() != 0)
            return $this->deleteById($model, $model->getId());
        return true;
    }

    public function deleteById(BaseDatabaseModel $model, $id)
    {
        $table = $this->getTableName($model);
        return $this->deleteInternal($table, $id);
    }

    private function addRelationsToSingle(BaseDatabaseModel $model)
    {
        foreach ($model->getDatabaseArray() as $key => $val) {
            if (strpos($key, "Id") !== false) {
                if ($val > 0) {
                    $objectName = str_replace("Id", "", $key);
                    $fullObjectName = ReflectionHelper::getInstance()->getNamespace($model) . "\\" . $objectName;
                    $obj = new $fullObjectName();
                    $relationObj = $this->GetById($obj, $val, false);
                    if ($relationObj !== false) {
                        $secMethod = "set" . $objectName;
                        $obj->$secMethod($relationObj);
                    }
                }
            }
        }
    }

    private function insertInternal($table, $arr)
    {
        $excludedArray = array();
        $sql = 'INSERT INTO ' . $table . ' ' . $this->constructMiddleSQL("insert", $arr, $excludedArray);
        if ($this->executeSql($sql, $arr))
            return $this->getLastInsertedId();
        return false;
    }

    private function updateInternal($table, $arr)
    {
        $excludedArray = array();
        $excludedArray[] = "Id";
        $sql = 'UPDATE ' . $table . ' SET ' . $this->constructMiddleSQL("update", $arr, $excludedArray) . ' WHERE Id = :Id';
        return $this->executeSql($sql, $arr);
    }

    private function deleteInternal($table, $id)
    {
        return $this->executeSql('DELETE FROM ' . $table . ' WHERE Id = :Id', array("Id" => $id));
    }

    /**
     * @param $table
     * @param bool $swallowFailures
     * @return bool|\PDOStatement
     */
    private function dropTableInternal($table, $swallowFailures = false)
    {
        return $this->executeSql('DROP TABLE ' . $table, null, false, $swallowFailures);
    }

    private function constructConditionSQL($params)
    {
        if ($params == null || !is_array($params) || count($params) == 0)
            return "";

        $sql = " WHERE ";
        foreach ($params as $key => $val) {
            $sql .= $key . " = :" . $key . " AND ";
        }
        $sql = substr($sql, 0, -4);
        return $sql;
    }

    private function constructMiddleSQL($mode, $params, $excluded = null)
    {
        $sql = "";
        if ($mode == "update") {
            foreach ($params as $key => $val) {
                if (!is_array($excluded) || !in_array($key, $excluded))
                    $sql .= $key . " = :" . $key . ", ";
            }
            $sql = substr($sql, 0, -2);
        } else if ($mode == "insert") {
            $part1 = "(";
            $part2 = "VALUES (";
            foreach ($params as $key => $val) {
                if (!is_array($excluded) || !in_array($key, $excluded)) {
                    $part1 .= $key . ", ";
                    $part2 .= ":" . $key . ", ";
                }
            }
            $part1 = substr($part1, 0, -2);
            $part2 = substr($part2, 0, -2);

            $part1 .= ") ";
            $part2 .= ")";
            $sql = $part1 . $part2;
        }
        return $sql;
    }

    /**
     * @param $model
     * @return bool|TableModel
     */
    private function getTableName($model)
    {
        $nameSpace = get_class($model);
        if (isset($this->tables[$nameSpace]))
            return $this->tables[$nameSpace];
        return false;
    }
}