<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 19:19
 */

namespace Famoser\phpSLWrapper\Framework\Models\DataService;


use Famoser\phpSLWrapper\Framework\Core\Reflection\AttributeReflectionClass;
use Famoser\phpSLWrapper\Framework\Core\Reflection\DatabaseAttributeProperty;
use function Famoser\phpSLWrapper\Framework\Helpers\JsonHelper\json_encode_with_json_properties;
use Famoser\phpSLWrapper\Framework\Services\DataService;

class EntityInfo extends EntityBase
{
    /* @database TEXT, notnull */
    private $className;
    /* @database TEXT, notnull */
    private $tableName;
    private $properties;
    /* @database TEXT, notnull */
    private $propertiesJson;

    public function __construct(EntityBase $entity = null)
    {
        parent::__construct();

        if ($entity != null)
        {
            $reflection = new AttributeReflectionClass(get_class($entity));
            $this->properties = $reflection->getPropertiesWithDatabaseAttributes();
            $this->propertiesJson = json_encode($this->properties);
            $this->className = $reflection->getName();
            $this->setTableName();
        }
    }

    private function setTableName(array $existing = null)
    {
        $this->tableName = substr($this->className, strripos($this->className, "\\") + 1);
        if ($existing != null) {
            $nameSpaceSplits = explode("\\", $this->className);
            unset($nameSpaceSplits[count($nameSpaceSplits) - 1]);
            while (in_array($this->tableName, $existing)) {
                $this->tableName = $nameSpaceSplits[count($nameSpaceSplits) - 1];
                unset($nameSpaceSplits[count($nameSpaceSplits) - 1]);
            }
        }
    }

    /**
     * @return DatabaseAttributeProperty[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    public function getAsCreateTableSQL($flavor = DataService::DRIVER_MYSQL)
    {
        $sql = "CREATE TABLE " . $this->tableName . " (";
        $endSQL = "";
        foreach ($this->properties as $property) {
            $sql .= $property->getAsSql($flavor). ",";
            if ($property->isPrimary())
                $endSQL .= $property->getAsPrimaryKeySql(). ",";
        }
        if ($endSQL != "") {
            $sql .= $endSQL;
        }
        $sql = substr($sql, 0, -1). ")";
        return $sql;
    }

    public function getClassName()
    {
        return $this->className;
    }
}