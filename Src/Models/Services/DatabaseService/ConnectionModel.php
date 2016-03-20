<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 23:38
 */

namespace famoser\phpFrame\Models\Services\DatabaseService;


use PDO;

class ConnectionModel
{
    private $connection;
    private $connectionName;
    private $connectionDriver;

    public function __construct(PDO $connection, $connectionName, $connectionDriver)
    {
        $this->connection = $connection;
        $this->connectionName = $connectionName;
        $this->connectionDriver = $connectionDriver;
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * @return int
     */
    public function getConnectionDriver()
    {
        return $this->connectionDriver;
    }


}