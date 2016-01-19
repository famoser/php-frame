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
use Famoser\phpSLWrapper\Framework\Core\Singleton\Singleton;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\assert_all_keys_exist;
use Famoser\phpSLWrapper\Framework\Models\DataService\EntityBase;
use Famoser\phpSLWrapper\Framework\Models\DataService\EntityInfo;
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
            return $this->initializeService();
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

    private function initializeService()
    {
        if ($this->getEntityInfos() === false) {
            Logger::getInstance()->logFatal("DataService could not initialize correctly, EntityInfos could not be read out");
            return false;
        }

        return true;
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
        $err = $this->GetDatabaseError($ex);
        if ($err === DataService::ERROR_TABLE_NOT_FOUND) {
            Logger::getInstance()->logException($ex, "Table not found");
        } else {
            Logger::getInstance()->logException($ex);
        }
        return false;
    }

    private function GetDatabaseError(Exception $ex)
    {
        if ($ex->getCode() == "42S02")
            return DataService::ERROR_TABLE_NOT_FOUND;
        return DataService::ERROR_UNKNOWN_ERROR;
    }

    /**
     * @return EntityInfo[]|bool
     */
    private function getEntityInfos()
    {
        $conn = $this->getConnection();
        if ($conn == false) {
            return false;
        }

        $info = new EntityInfo(new EntityInfo());
        try {
            $res = $conn->prepare("SELECT * FROM EntityInfo");
            //mysql implementation
            $res->execute();
            $res = $this->fetchAllToClass($res, $info);
            if (count($res) == 0) {
                $this->insertObject($info, $info);
                return $this->getEntityInfos();
            } else {
                return $res;
            }
        } catch (Exception $ex) {
            $error = $this->GetDatabaseError($ex);
            if ($error == DataService::ERROR_TABLE_NOT_FOUND) {
                //create table and insert EntityInfo
                if ($this->createTableForObject($info))
                    return $this->getEntityInfos();
            } else {
                return $this->EvaluateDatabaseException($ex);
            }
        }
        return false;
    }

    private function createTableForObject(EntityBase $obj)
    {
        $info = new EntityInfo($obj);
        $sql = $info->getAsCreateTableSQL($this->connectionType);
        try {
            $con = $this->getConnection()->prepare($sql);
            return $con->execute();
        } catch (Exception $ex) {
            return $this->EvaluateDatabaseException($ex);
        }
    }

    private function insertObject(EntityBase $obj, EntityInfo $nfo = null)
    {
        if ($nfo == null)
            $nfo = $this->getEntityInfoForObject($obj);

        if ($nfo === false) {
            Logger::getInstance()->logError("Could not find EntityInfo for object", $obj);
            return false;
        }

        if ($nfo != null) {
            $info = new EntityInfo($obj);
            $sql = $info->getAsCreateTableSQL($this->connectionType);
            try {
                $con = $this->getConnection()->prepare($sql);
                return $con->execute();
            } catch (Exception $ex) {
                return $this->EvaluateDatabaseException($ex);
            }
        }
    }

    /**
     * @param EntityBase $obj
     * @return EntityInfo|bool
     */
    private function getEntityInfoForObject(EntityBase $obj)
    {
        //todo
        return false;
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
            Logger::getInstance()->logError("Unknown connection Type. Switching to MY_SQL as default.");
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


    private function fetchAllToClass(PDOStatement $executedStatement, EntityInfo $obj)
    {
        return $executedStatement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $obj->getClassName());
    }
}