<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 11:50
 */

namespace Famoser\phpSLWrapper\Framework\Services;


use Exception;
use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;
use Famoser\phpSLWrapper\Framework\Core\Reflection\AttributeReflectionClass;
use Famoser\phpSLWrapper\Framework\Core\Reflection\DatabaseAttributeProperty;
use Famoser\phpSLWrapper\Framework\Core\Singleton\Singleton;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\assert_all_keys_exist;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\createDeleteSql;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\createInsertSql;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\createTableExistSql;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\createTableSql;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\createUpdateSql;
use Famoser\phpSLWrapper\Framework\Models\DataService\EntityBase;
use Famoser\phpSLWrapper\Framework\Models\DataService\EntityInfo;
use Famoser\phpSLWrapper\Framework\Models\DataService\TableInfo;
use PDO;
use PDOStatement;

class DataService extends Singleton
{
    const ERROR_CONNECTION_NULL = 10000;
    const ERROR_UNSUPPORTED_CONNECTION_DRIVER = 10001;

    const DRIVER_MYSQL = 10100;
    const DRIVER_SQLITE = 10101;

    //internal errors
    const ERROR_TABLE_NOT_FOUND = 10200;
    const ERROR_UNKNOWN_ERROR = 10299;

    const FORMAT_DATETIME = "Y-m-d H:i:s";
    const FORMAT_DATE = "Y-m-d";
    const FORMAT_TIME = "H:i:s";

    public function __construct()
    {
        $settings = SettingsService::getInstance()->getValueFor("DataService");
        $primary = null;
        if (count($settings) == 1) {
            $primary = $settings[0];
        }
        if (count($settings) > 1)
            foreach ($settings as $setting) {
                if ($setting["primary"] === true) {
                    $primary = $setting;
                    break;
                }
            } else {
            Logger::getInstance()->logError("No configuration 'DataService' defined, DataService cannot initialize");
            return;
        }

        if ($primary != null) {
            if ($primary["type"] == "mysql") {
                $params = array("host", "database", "user", "password");
                if (!assert_all_keys_exist($primary, $params)) {
                    Logger::getInstance()->logInfo("Cannot open MySQl conenction because not all required parameters are defined", $params);
                } else {
                    $this->createConnection("mysql:host=" . $primary["host"] . ";dbname=" . $primary["database"] . ";charset=utf8", $primary["user"], $primary["password"]);
                }
            } else if ($primary["type"] == "sqlite") {
                $path = SettingsService::getInstance()->getValueFor(SettingsService::DATA_DIR) . SettingsService::getInstance()->getValueFor(SettingsService::DIRECTORY_SEPARATOR) . $primary["path"];
                if (file_exists($path)) {
                    $this->createConnection("sqlite:" . $path);
                } else if ($primary["createIfNotExisting"]) {
                    Logger::getInstance()->logInfo("New sqlite Database created");
                    $this->createConnection("sqlite:" . $path);
                } else {
                    Logger::getInstance()->logFatal("Sqlite Database not found at " . $path);
                }
            } else {
                Logger::getInstance()->logError("Unknown type: " . $primary["type"], $primary);
            }
        } else {
            Logger::getInstance()->logError("No valid configuration found. If you've defined more than one DatabaseConfiguration, mark one as primary");
            return;
        }
    }

    private function createConnection($host, $user = "", $password = "")
    {
        try {
            $this->setConnection(new PDO($host, $user, $password));
            $this->setAttributes($this->getConnection());
            return true;
        } catch (\Exception $ex) {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function setAttributes(PDO $connection)
    {
        if (SettingsService::getInstance()->getBuildType() == SettingsService::BUILD_TYPE_DEBUG) {
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } else if (SettingsService::getInstance()->getBuildType() == SettingsService::BUILD_TYPE_TEST) {
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } else if (SettingsService::getInstance()->getBuildType() == SettingsService::BUILD_TYPE_RELEASE) {
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } else {
            Logger::getInstance()->logFatal("Unknown Build Type!");
        }

        if ($this->connectionType == DataService::DRIVER_MYSQL) {
            $connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }
    }

    private function ReturnDatabaseError($const)
    {
        if ($const === DataService::ERROR_CONNECTION_NULL) {
            Logger::getInstance()->logError("Connection is null, cannot execute Task");
            return false;
        }
        Logger::getInstance()->logError("Unknown Error occurred, cannot execute Task");
        return false;
    }

    private function EvaluateDatabaseException(Exception $ex)
    {
        $err = $this->getDatabaseError($ex);
        if ($err === DataService::ERROR_TABLE_NOT_FOUND) {
            Logger::getInstance()->logException($ex, "Table not found");
        } else {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function getDatabaseError(Exception $ex)
    {
        if ($ex->getCode() == "42S02")
            return DataService::ERROR_TABLE_NOT_FOUND;
        return DataService::ERROR_UNKNOWN_ERROR;
    }

    /**
     * @param EntityBase $entity
     * @return bool
     */
    public function saveToDatabase(EntityBase $entity)
    {
        if ($entity->IsInDatabase()) {
            return $this->updateEntity($entity);
        } else {
            return $this->insertEntity($entity);
        }
    }

    /**
     * @param EntityBase[] $entity
     * @return bool
     */
    public function saveAllToDatabase(array $entity)
    {
        $res = true;
        foreach ($entity as $item) {
            $res &= $this->saveToDatabase($item);
        }
        return $res;
    }

    /**
     * @param EntityBase $entity
     * @return bool
     */
    public function deleteFromDatabase(EntityBase $entity)
    {
        if ($entity->IsInDatabase()) {
            return $this->deleteEntity($entity);
        } else {
            return true;
        }
    }

    public function getFromDatabase(array $condition, array $orderBy)
    {

    }

    private $tableInfo = array();

    private function getTableInfo(EntityBase $entity)
    {
        $class = get_class($entity);
        if (isset($this->tableInfo[$class])) {
            return $this->tableInfo[$class];
        } else {
            $tableInfo = new TableInfo($entity);
            if (!$tableInfo->getIsEvaluated()) {
                $this->correctTable($tableInfo);
                $tableInfo->setIsEvaluated(true);
            }
            $this->tableInfo[$class] = $tableInfo;
            return $tableInfo;
        }
    }

    private function correctTable(TableInfo $info)
    {
        try {
            //check if table exists
            $sql = createTableExistSql($info, $this->connectionType);
            $res = $this->getConnection()->prepare($sql);
            if ($res->execute()) {
                //todo: correct faulty table
                return true;
            }
        } catch (Exception $ex) {
            $err = $this->getDatabaseError($ex);
            if ($err == DataService::ERROR_TABLE_NOT_FOUND) {
                //easy peasy
                $sql = createTableSql($info);
                return $this->executeStatement($sql);
            }
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function executeStatement($statement)
    {
        try {
            $res = $this->getConnection()->prepare($statement);
            return $res->execute();
        } catch (Exception $ex) {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function updateEntity(EntityBase $entity)
    {
        $nfo = $this->getTableInfo($entity);
        $sql = createUpdateSql($nfo, $this->connectionType);
        $arr = $this->getPropertyArray($entity);

        try {
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($arr);
        } catch (Exception $ex) {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function insertEntity(EntityBase $entity)
    {
        $nfo = $this->getTableInfo($entity);
        $sql = createInsertSql($nfo, $this->connectionType);
        $arr = $this->getPropertyArray($entity, true);

        try {
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($arr);
        } catch (Exception $ex) {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function deleteEntity(EntityBase $entity)
    {
        $nfo = $this->getTableInfo($entity);
        $sql = createDeleteSql($nfo, $this->connectionType);

        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue("Id", $entity->getId());
            return $stmt->execute();
        } catch (Exception $ex) {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    /**
     * @param EntityBase $entity
     * @param bool $excludeId
     * @return array
     */
    private function getPropertyArray(EntityBase $entity, bool $excludeId = false)
    {
        $nfo = $this->getTableInfo($entity);
        $arr = (array)$entity;
        $res = array();
        foreach ($nfo->getProperties() as $property) {
            $res[$property->getName()] = $property->convertPropertyValueForDatabase($arr[$property->getName()]);
        }
        if ($excludeId && isset($res["Id"]))
            unset($res["Id"]);

        return $res;
    }


    private $connection;
    private $connectionType;

    /**
     * @param PDO $connection
     */
    private function setConnection(PDO $connection)
    {
        $this->connection = $connection;
        $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql') {
            $this->connectionType = DataService::DRIVER_MYSQL;
        } else if ($driver == "sqlite") {
            $this->connectionType = DataService::DRIVER_SQLITE;
        } else {
            $this->connectionType = DataService::DRIVER_MYSQL;
            Logger::getInstance()->logError("Unknown connection Type. Switching to MYSQL as default.");
        }
    }

    /**
     * @return PDO
     */
    private function getConnection()
    {
        if ($this->connection == null)
            return $this->ReturnDatabaseError(DataService::ERROR_CONNECTION_NULL);
        return $this->connection;
    }


    private function fetchAllToClass(PDOStatement $executedStatement, TableInfo $obj)
    {
        return $executedStatement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $obj->getClassName());
    }
}