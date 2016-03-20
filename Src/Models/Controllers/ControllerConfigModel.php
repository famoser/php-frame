<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 16.02.2016
 * Time: 17:56
 */

namespace famoser\phpFrame\Models\Controllers;


use famoser\phpFrame\Helpers\ReflectionHelper;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;

class ControllerConfigModel
{
    private $instance;
    private $name;
    private $friendlyName;
    private $controllerLink;

    private $listName;
    private $listFilter = array();
    private $listOrderBy;
    private $listLoadRelations = true;
    private $listLoadEnabled = true;

    private $crudForbiddenProperties = array();
    private $crudDefaultProperties = array();
    private $crudOverWriteProperties = array();

    private $oneNChildren = array();
    private $nOneParents = array();

    public function __construct(BaseDatabaseModel $model, $friendlyName, $controllerLink = null)
    {
        $this->instance = $model;
        $this->name = ReflectionHelper::getInstance()->getObjectName($model);;
        $this->friendlyName = $friendlyName;

        //set defaults
        $this->listName = $friendlyName;
        if ($controllerLink == null)
            $this->controllerLink = strtolower($friendlyName . "s");
        else
            $this->controllerLink = $controllerLink;
    }

    /**
     * pass null to keep default
     * @param null $listLoadEnabled
     * @param null $listName
     * @param array|null $listFilter
     * @param null $listOrderBy
     * @param null $listLoadRelations
     */
    public function configureList($listLoadEnabled = null, $listName = null, array $listFilter = null, $listOrderBy = null, $listLoadRelations = null)
    {
        if ($listLoadEnabled !== null)
            $this->listLoadEnabled = $listLoadEnabled;
        if ($listName !== null)
            $this->listName = $listName;
        if (is_array($listFilter))
            $this->listFilter = $listFilter;
        if ($listOrderBy !== null)
            $this->listOrderBy = $listOrderBy;
        if ($listLoadRelations != null)
            $this->listLoadRelations = $listLoadRelations;
    }

    /**
     * pass null to keep default
     * @param array $defaultProperties
     * @param array|null $allowedProperties
     * @param array|null $overWriteProperties
     */
    public function configureCrud(array $defaultProperties, array $allowedProperties = null, array $overWriteProperties = null)
    {
        if (is_array($allowedProperties))
            $this->crudForbiddenProperties = $allowedProperties;
        if (is_array($defaultProperties))
            $this->crudDefaultProperties = $defaultProperties;
        if (is_array($overWriteProperties))
            $this->crudOverWriteProperties = $overWriteProperties;
    }

    public function addOneNChild(ControllerConfigModel $config)
    {
        $this->oneNChildren[] = $config;
    }

    public function addOneNParent(ControllerConfigModel $config)
    {
        $this->nOneParents[] = $config;
    }

    /**
     * @return BaseDatabaseModel
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * @return string
     */
    public function getMultiplyFriendlyName()
    {
        return $this->friendlyName . "s";
    }

    /**
     * @return array
     */
    public function getListFilter()
    {
        return $this->listFilter;
    }

    /**
     * @return string
     */
    public function getListOrderBy()
    {
        return $this->listOrderBy;
    }

    /**
     * @return mixed
     */
    public function getListLoadRelations()
    {
        return $this->listLoadRelations;
    }

    /**
     * @return array
     */
    public function getCrudForbiddenProperties()
    {
        return $this->crudForbiddenProperties;
    }

    /**
     * @return array
     */
    public function getCrudForbiddenPropertiesAsNullArray()
    {
        $res = array();
        foreach ($this->getCrudForbiddenProperties() as $crudForbiddenProperty) {
            $res[$crudForbiddenProperty] = null;
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getCrudDefaultProperties()
    {
        return $this->crudDefaultProperties;
    }

    /**
     * @return array
     */
    public function getCrudOverWriteProperties()
    {
        return $this->crudOverWriteProperties;
    }

    /**
     * @return string
     */
    public function getSingleListName()
    {
        return $this->listName;
    }

    /**
     * @return string
     */
    public function getMultipleListName()
    {
        return $this->listName . "s";
    }

    /**
     * @return ControllerConfigModel[]
     */
    public function getOneNChildren()
    {
        return $this->oneNChildren;
    }

    /**
     * @param BaseDatabaseModel $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * @return ControllerConfigModel[]
     */
    public function getNOneParents()
    {
        return $this->nOneParents;
    }

    /**
     * @return mixed
     */
    public function getListLoadEnabled()
    {
        return $this->listLoadEnabled;
    }

    /**
     * @return null
     */
    public function getControllerLink()
    {
        return $this->controllerLink;
    }
}