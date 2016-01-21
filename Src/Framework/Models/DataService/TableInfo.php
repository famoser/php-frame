<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 20.01.2016
 * Time: 15:09
 */

namespace Famoser\phpSLWrapper\Framework\Models\DataService;


use Famoser\phpSLWrapper\Framework\Core\Reflection\AttributeReflectionClass;
use Famoser\phpSLWrapper\Framework\Core\Reflection\DatabaseAttributeProperty;

class TableInfo
{
    private $className;
    private $tableName;
    private $properties;
    private $isEvaluated;

    public function __construct(EntityBase $entity)
    {
        $reflection = new AttributeReflectionClass(get_class($entity));
        $this->properties = $reflection->getPropertiesWithDatabaseAttributes();
        $this->className = $reflection->getName();
        $this->tableName = str_replace("\\", "", $this->className);
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
     * @return bool
     */
    public function getIsEvaluated()
    {
        return $this->isEvaluated;
    }

    /**
     * @param bool $isEvaluated
     */
    public function setIsEvaluated($isEvaluated)
    {
        $this->isEvaluated = $isEvaluated;
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
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}