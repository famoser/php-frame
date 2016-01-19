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

class DatabaseAttributeProperty implements \JsonSerializable
{
    /* @json */
    private $Name;
    /* @json */
    private $Type;
    /* @json */
    private $NotNull;
    /* @json */
    private $AutoIncrement;
    /* @json */
    private $Primary;

    public function __construct($name, $type, $notNull = false, $autoIncrement = false, $primary = false)
    {
        $this->Name = $name;
        $this->Type = $type;
        $this->NotNull = $notNull;
        $this->AutoIncrement = $autoIncrement;
        $this->Primary = $primary;
    }

    public function getAsSql($flavour = DataService::DRIVER_MYSQL)
    {
        $sql = $this->Name . " " . $this->Type;
        if ($this->NotNull)
            $sql .= "  NOT NULL";
        if ($this->AutoIncrement) {
            if ($flavour == DataService::DRIVER_SQLITE)
                $sql .= " AUTOINCREMENT";
            else if ($flavour == DataService::DRIVER_MYSQL)
                $sql .= " AUTO_INCREMENT";
            else {
                Logger::getInstance()->logError("Unknown Database Driver: ".$flavour. ". Using AUTOINCREMENT.");
                $sql .= " AUTOINCREMENT";
            }
        }
        return $sql;
    }

    public function isPrimary()
    {
        return $this->Primary;
    }

    public function getAsPrimaryKeySql()
    {
        return "PRIMARY KEY (" . $this->Name . ")";
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
            'Name' => $this->Name,
            'Type' => $this->Type,
            'NotNull' => $this->NotNull,
            'AutoIncrement' => $this->AutoIncrement,
            'Primary' => $this->Primary
        ];
    }
}