<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 11:02
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\ReflectionHelper;
use famoser\phpFrame\Models\Controllers\ControllerConfigModel;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Models\Database\BaseModel;
use famoser\phpFrame\Models\View\MenuItem;
use famoser\phpFrame\Services\DatabaseService;
use famoser\phpFrame\Services\GenericDatabaseService;
use famoser\phpFrame\Views\GenericCrudView;
use famoser\phpFrame\Views\GenericView;
use famoser\phpFrame\Views\ViewBase;

abstract class GenericControllerBase extends MenuControllerBase
{
    private $crudReplaces = null;
    private $controllerName;
    private $defaultFriendlyObjectName;

    private $additionalViewProps = array();
    private $editObjects = array();

    //same values in GenericCrudView!
    const CRUD_CREATE = 1;
    const CRUD_READ = 2;
    const CRUD_UPDATE = 3;
    const CRUD_DELETE = 4;

    /**
     * GenericController constructor.
     * @param $request
     * @param $params
     * @param $files
     * @param array|null example : array(Generic1nController::CRUD_CREATE => Generic1nController::CRUD_READ)
     * @param array|null example : array("admins" => array(new AdminModel(), array("IsCompleted" => true), "Name"))
     */
    public function __construct($request, $params, $files, $defaultFriendlyObjectName, array $crudReplaces = null)
    {
        parent::__construct($request, $params, $files);

        $this->defaultFriendlyObjectName = $defaultFriendlyObjectName;
        $this->crudReplaces = $crudReplaces;
        $this->controllerName = ReflectionHelper::getInstance()->getObjectName($this);
    }

    /**
     * @return mixed
     */
    public function getAdditionalViewProps()
    {
        return $this->additionalViewProps;
    }

    /**
     * @param array $additionalViewProps
     */
    public function setAdditionalViewProps(array $additionalViewProps)
    {
        $this->additionalViewProps = $additionalViewProps;
    }

    protected function addControllerConfig(ControllerConfigModel $config)
    {
        $this->editObjects[] = $config;
    }

    /**
     * @return ControllerConfigModel[]
     */
    protected function getEditObjects()
    {
        return $this->editObjects;
    }

    /**
     * @param null $customParams
     */
    public function Display($customParams = null)
    {
        if (is_array($customParams))
            $params = $customParams;
        else
            $params = $this->params;

        if (count($params) == 0 || $params[0] == "") {
            $view = new GenericView($this->controllerName);

            foreach ($this->getEditObjects() as $item) {
                if ($item->getListLoadEnabled())
                $view->assign($item->getMultipleListName(), GenericDatabaseService::getInstance()->getAll($item->getInstance(), $item->getListFilter(), $item->getListLoadRelations(), $item->getListOrderBy()));
            }

            return $this->returnView($view);
        } else {
            if (count($params) > 0) {
                if ($params[0] == "add") {
                    if (isset($this->request["add"]) && $this->request["add"] == "true") {
                        $req = $this->request;
                        $successful = true;

                        $newIds = array();
                        foreach ($this->getEditObjects() as $editObject) {
                            ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudDefaultProperties());
                            ReflectionHelper::getInstance()->writeFromPostArrayToObjectProperties($editObject->getInstance(), $req[$editObject->getName()]);
                            ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudForbiddenPropertiesAsNullArray());
                            ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudOverWriteProperties());
                            $res = GenericDatabaseService::getInstance()->create($editObject->getInstance());
                            if ($res === false) {
                                LogHelper::getInstance()->logError($editObject->getFriendlyName() . " could not be added");
                                $successful = false;
                            } else {
                                $newIds[$editObject->getName()] = $res;
                            }
                        }

                        //set relation Id's
                        foreach ($this->getEditObjects() as $editObject) {
                            if (ReflectionHelper::getInstance()->addRelationPropertiesToObject($editObject->getInstance(), $newIds)) {
                                $res = GenericDatabaseService::getInstance()->update($editObject->getInstance());
                                if ($res === false) {
                                    LogHelper::getInstance()->logError($editObject->getFriendlyName() . " relations could not be updated");
                                    $successful = false;
                                }
                            }
                        }


                        if ($successful) {
                            LogHelper::getInstance()->logUserInfo($this->defaultFriendlyObjectName . " was added");
                            $this->exitWithControllerRedirect("update/" . $this->getEditObjects()[0]->getInstance()->getId());
                        } else {
                            //remove all failed objects to keep database clean
                            foreach ($this->getEditObjects() as $editObject) {
                                GenericDatabaseService::getInstance()->delete($editObject->getInstance());
                            }
                        }
                    }

                    $view = new GenericCrudView($this->controllerName, $this->getFilenameFromMode($this->getMode(GenericControllerBase::CRUD_CREATE)));
                    $this->addEditObjectsPropertiesToView($view);

                    return $this->returnView($view);
                }
            }
            if (count($params) > 1 && isset($params[1]) && is_numeric($params[1])) {
                if ($params[0] == "read") {
                    if ($this->fillInstancesWithPassedId($params[1])) {
                        $view = new GenericCrudView($this->controllerName, $this->getFilenameFromMode($this->getMode(GenericControllerBase::CRUD_READ)));
                        $this->addEditObjectsPropertiesToView($view);

                        return $this->returnView($view);
                    } else {
                        return $this->returnFailure(ControllerBase::FAILURE_NOT_FOUND);
                    }
                } else if ($params[0] == "update") {
                    if ($this->fillInstancesWithPassedId($params[1])) {
                        if (isset($this->request["update"]) && $this->request["update"] == "true") {
                            $req = $this->request;
                            $successful = true;

                            foreach ($this->getEditObjects() as $editObject) {
                                ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudDefaultProperties());
                                ReflectionHelper::getInstance()->writeFromPostArrayToObjectProperties($editObject->getInstance(), $req[$editObject->getName()]);
                                ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudForbiddenPropertiesAsNullArray());
                                ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudOverWriteProperties());
                                $res = GenericDatabaseService::getInstance()->update($editObject->getInstance());
                                if ($res === false) {
                                    LogHelper::getInstance()->logError($editObject->getFriendlyName() . " could not be updated");
                                    $successful = false;
                                }
                            }

                            if ($successful)
                                LogHelper::getInstance()->logUserInfo($this->defaultFriendlyObjectName . " was updated");
                            else
                                LogHelper::getInstance()->logError($this->defaultFriendlyObjectName . " could not be updated");
                        }

                        $view = new GenericCrudView($this->controllerName, $this->getFilenameFromMode($this->getMode(GenericControllerBase::CRUD_UPDATE)));
                        $this->addEditObjectsPropertiesToView($view);

                        return $this->returnView($view);
                    } else
                        return $this->returnFailure(ControllerBase::FAILURE_NOT_FOUND);
                } else if ($params[0] == "delete") {

                    if ($this->fillInstancesWithPassedId($params[1])) {
                        if (isset($this->request["delete"]) && $this->request["delete"] == "true") {
                            $req = $this->request;
                            $successful = true;

                            foreach ($this->getEditObjects() as $editObject) {
                                ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudDefaultProperties());
                                ReflectionHelper::getInstance()->writeFromPostArrayToObjectProperties($editObject->getInstance(), $req[$editObject->getName()]);
                                ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudForbiddenPropertiesAsNullArray());
                                ReflectionHelper::getInstance()->writeArrayIntoObject($editObject->getInstance(), $editObject->getCrudOverWriteProperties());
                                $res = GenericDatabaseService::getInstance()->delete($editObject->getInstance());
                                if ($res === false) {
                                    LogHelper::getInstance()->logError($editObject->getFriendlyName() . " could not be deleted");
                                    $successful = false;
                                }
                            }

                            if ($successful)
                                LogHelper::getInstance()->logUserInfo($this->defaultFriendlyObjectName . " was deleted");
                            else
                                LogHelper::getInstance()->logError($this->defaultFriendlyObjectName . " could not be deleted");
                        }

                        $view = new GenericCrudView($this->controllerName, $this->getFilenameFromMode($this->getMode(GenericControllerBase::CRUD_DELETE)));
                        $this->addEditObjectsPropertiesToView($view);

                        return $this->returnView($view);
                    } else
                        return $this->returnFailure(ControllerBase::FAILURE_NOT_FOUND);


                }
            }
        }

        return parent::Display();
    }

    public function DisplayExtended($customParams = null)
    {
        if (is_array($customParams))
            $params = $customParams;
        else
            $params = $this->params;

        if (count($params) > 1 && isset($params[1]) && is_numeric($params[1])) {
            if (strpos($params[0], "by") === 0) {
                $view = new GenericCrudView($this->controllerName, $this->getFilenameFromMode($this->getMode(GenericControllerBase::CRUD_UPDATE)));
                $this->addEditObjectsPropertiesToView($view);
                $found = false;

                $parentName = substr($params[0], 2);
                foreach ($this->getEditObjects() as $editObject) {
                    foreach ($editObject->getNOneParents() as $parent) {
                        if ($parent->getName() == $parentName) {
                            if (!$found) {
                                $view->assign($parent->getSingleListName(), GenericDatabaseService::getInstance()->getById($parent->getInstance(), $params[1], $parent->getListLoadRelations()));
                                $found = true;
                            }
                            $view->assign($editObject->getMultipleListName(),
                                GenericDatabaseService::getInstance()->getAll($editObject->getInstance(),
                                    array($parentName . "Id" => $params[1]),
                                    $editObject->getListLoadRelations(),
                                    $editObject->getListOrderBy()));

                        }
                    };
                }
                if ($found)
                    return $this->returnView($view);
            } else if (strpos($params[0], "addTo") === 0) {
                $parentName = substr($params[0], 5);
                foreach ($this->getEditObjects() as $editObject) {
                    foreach ($editObject->getNOneParents() as $parent) {
                        if ($parent->getName() == $parentName) {
                            ReflectionHelper::getInstance()->setPropertyOfObject($editObject->getInstance(), $parentName . "Id", $params[1]);
                        }
                    }
                }
                $params[0] = "add";
            }
        }
        return self::Display($params);
    }

    private function getMode($mode)
    {
        if ($this->crudReplaces != null && is_array($this->crudReplaces) && isset($this->crudReplaces[$mode]))
            return $this->crudReplaces[$mode];
        else
            return $mode;
    }

    private function getFilenameFromMode($mode)
    {
        if ($mode == GenericControllerBase::CRUD_CREATE)
            return "create";
        else if ($mode == GenericControllerBase::CRUD_READ)
            return "read";
        else if ($mode == GenericControllerBase::CRUD_UPDATE)
            return "update";
        else if ($mode == GenericControllerBase::CRUD_DELETE)
            return "delete";
        else
            LogHelper::getInstance()->logFatal("Invalid crud action! Please use one of the constants in GenericController");
        return "";
    }

    private function addEditObjectsPropertiesToView(ViewBase $view)
    {
        foreach ($this->getEditObjects() as $editObject) {
            $view->assign($editObject->getSingleListName(), $editObject->getInstance());
            foreach ($editObject->getOneNChildren() as $item) {
                $view->assign($item->getMultipleListName(), GenericDatabaseService::getInstance()->getAll($item->getInstance(), $item->getListFilter(), $item->getListLoadRelations(), $item->getListOrderBy()));
            }
        }
    }

    private function fillInstancesWithPassedId($id)
    {
        $mainObj = GenericDatabaseService::getInstance()->getById($this->getEditObjects()[0]->getInstance(), $id);

        if ($mainObj !== false) {
            $this->getEditObjects()[0]->setInstance($mainObj);
            //get all other models
            for ($i = 1; $i < count($this->getEditObjects()); $i++) {
                $newId = ReflectionHelper::getInstance()->getPropertyOfObjects($this->getEditObjects(), $this->getEditObjects()[$i]->getName() . "Id", 0, $i);
                if ($newId != null) {
                    $this->getEditObjects()[$i]->setInstance(GenericDatabaseService::getInstance()->getById($this->getEditObjects()[$i]->getInstance(), $newId));
                } else {
                    LogHelper::getInstance()->logError("can't find the id of " . $this->getEditObjects()[$i]->getFriendlyName());
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    protected function returnView(ViewBase $view)
    {
        if (is_array($this->getAdditionalViewProps())) {
            foreach ($this->getAdditionalViewProps() as $key => $val) {
                $view->assign($key, $val);
            }
        }
        return parent::returnView($view);
    }
}