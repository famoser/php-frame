<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 08.09.2015
 * Time: 15:59
 */

namespace famoser\phpFrame\Helpers;

use DateTime;
use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Logging\LogItem;
use famoser\phpFrame\Helpers\HelperBase;
use famoser\phpFrame\Helpers\ReflectionHelper;
use famoser\phpFrame\Interfaces\Models\IModel;
use famoser\phpFrame\Models\Controllers\ControllerConfigModel;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Models\Database\BaseModel;
use famoser\phpFrame\Services\RouteService;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Views\ViewBase;

class PartHelper extends HelperBase
{
    const PART_HEAD = 10;
    const PART_FOOTER_CENTER = 11;
    const PART_FOOTER_CONTENT = 11;
    const PART_FOOTER_CRUD = 12;
    const PART_HEADER_CENTER = 13;
    const PART_HEADER_CONTENT = 14;
    const PART_HEADER_CRUD = 15;
    const PART_LOADING_PLACEHOLDER = 16;
    const PART_MENU = 17;
    const PART_MESSAGES = 18;

    const DIALOG_CREATE = 20;
    const DIALOG_READ = 21;
    const DIALOG_UPDATE = 22;
    const DIALOG_DELETE = 23;

    /**
     * @param IModel $obj
     * @param string $prop
     * @param string|null $display
     * @param string $type
     * @param string|null $customPlaceholder
     * @param IModel[]|null $special
     * @return string
     */
    public function getInput($obj, $prop, $display = null, $type = "text", $customPlaceholder = null, array $special = null)
    {
        if ($type == "hidden") {
            return $this->getHiddenInput($obj, $prop);
        }

        if ($display == null)
            $display = $prop;

        if ($customPlaceholder != null)
            $placeholder = ' placeholder="' . $customPlaceholder . '" ';
        else
            $placeholder = ' placeholder="' . $prop . '" ';

        $val = ReflectionHelper::getInstance()->getPropertyOfObject($obj, $prop);
        $propName = $this->getPropertyName($obj, $prop);

        $html = '<label for="' . $prop . '">' . $display . '</label><br/>';
        if ($type == "textarea") {
            $html .= '<textarea' . $placeholder . ' class="interactive" id="' . $prop . '" name="' . $propName . '">' . $val . '</textarea>';
        } else if ($type == "select" || (strpos($type, "multiple") !== false && strpos($type, "select") !== false)) {
            $html .= '<select' . $placeholder . ' name="' . $propName . '" id="' . $prop . '"';
            if (strpos($type, "multiple") !== false) {
                $html .= 'multiple';
            }
            $html .= '>';
            foreach ($special as $item) {
                $add = "";
                if ($item->getId() == $val)
                    $add = " selected";
                $html .= '<option value="' . $item->getId() . '"' . $add . '>' . $item->getIdentification() . '</option>';
            }
            $html .= '</select>';
        } else {

            $html .= '<input' . $placeholder . ' id="' . $prop . '" name="' . $propName . '" type="' . $type . '"';
            if ($type == "checkbox") {
                if ($val == 1) {
                    $html .= ' checked="checked" value="true"';
                }
            } else {
                if ($val != null) {
                    if ($type == "date") {
                        $html .= ' value="' . FormatHelper::getInstance()->date($val) . '"';
                    } else if ($type == "datetime") {
                        $html .= ' value="' . FormatHelper::getInstance()->dateTime($val) . '"';
                    } else if ($type == "time") {
                        $html .= ' value="' . FormatHelper::getInstance()->time($val) . '"';
                    } else {
                        $html .= ' value="' . $val . '"';
                    }
                }
            }
            $html .= ">";
            if ($type == "checkbox")
                $html .= $this->addHiddenInput($obj, $prop . "CheckboxPlaceholder", true);
        }

        return $html;
    }

    private function formatProperty($value, $propName)
    {
        if (str_starts_with($propName, "Is")) {
            return $value == 1 ? "true" : "false";
        } else if (str_ends_with($propName, "Date")) {
            return FormatHelper::getInstance()->date($value);
        } else if (str_ends_with($propName, "DateTime")) {
            return FormatHelper::getInstance()->dateTime($value);
        } else if (str_ends_with($propName, "Time")) {
            return FormatHelper::getInstance()->time($value);
        } else {
            return $value;
        }
    }

    public function getControllerLink($href)
    {
        if (str_starts_with($href, "/")) {
            return $href;
        } else {
            return RuntimeService::getInstance()->getRouteUrlWithoutController() . "/" . $href;
        }
    }

    /**
     * @param $baseHref
     * @param $id
     * @param array $types
     * @param ControllerConfigModel $baseLinks
     * @return string
     */
    public function getDialogButtons($baseHref, $id, array $types, $baseLinks = null)
    {
        $html = '<div class="btn-group">';

        $editButtons = array();
        foreach ($types as $type) {
            $link = "create";
            if ($type == PartHelper::DIALOG_UPDATE) {
                $link = "update";
            } else if ($type == PartHelper::DIALOG_READ) {
                $link = "read";
            } else if ($type == PartHelper::DIALOG_DELETE) {
                $link = "delete";
            }
            $editButtons[] = $link . '_button_' . $id;
        }

        foreach ($types as $type) {
            $html .= $this->getSingleButton($baseHref, $id, $editButtons, $type);
        }

        /*
        if ($baseLinks != null) {
            foreach ($baseLinks->getOneNChildren() as $oneNChild) {
                $html .= $this->getSingleButton(PartHelper::getInstance()->getControllerLink($oneNChild->getControllerLink()), $id, $editButtons, PartHelper::DIALOG_CREATE);
            }
        }
        */

        $html .= '</div>';
        return $html;
    }

    private function getSingleButton($baseHref, $id, $buttonIds, $dialogType)
    {
        $link = "read";
        $icon = "flaticon-notes26";
        $text = "read";
        $type = "info";
        $colorClass = "info-action";
        if ($dialogType == PartHelper::DIALOG_CREATE) {
            $link = "create";
            $icon = "flaticon-add13";
            $text = "edit";
            $type = "warning";
            $colorClass = "warning-action";
        } else if ($dialogType == PartHelper::DIALOG_UPDATE) {
            $link = "update";
            $icon = "flaticon-pencil124";
            $text = "edit";
            $type = "warning";
            $colorClass = "warning-action";
        } else if ($dialogType == PartHelper::DIALOG_DELETE) {
            $link = "delete";
            $icon = "flaticon-cancel22";
            $text = "delete";
            $type = "error";
            $colorClass = "error-action";
        }

        $html = "";

        for ($i = 0; $i < count($buttonIds); $i++) {
            $html .= 'data-dialog-idbutton' . $id . '="' . $buttonIds[$i] . '" ';
        }

        return '<a id="' . $link . '_button_' . $id . '"
                   href="' . $baseHref . "/" . $link . "/" . $id . '"
                   class="tilebutton dialogbutton onlyicon ' . $colorClass . '"
                   ' . $html . '
                   data-dialog-title="' . $text . '"
                   data-dialog-type="' . $type . '"
                   data-dialog-size="wide"
                   role="button">
                    <span class="' . $icon . '">' . $text . '</span>
                </a>';
    }

    public function getHiddenKeyValue($key, $value)
    {
        return '<input type="hidden" name="' . $key . '" value="' . $value . '">';
    }

    public function getHiddenInput($obj, $prop)
    {
        $propName = $this->getPropertyName($obj, $prop);
        $value = ReflectionHelper::getInstance()->getPropertyOfObject($obj, $prop);
        return '<input type="hidden" name="' . $propName . '" value="' . $value . '">';
    }

    private function addHiddenInput($obj, $prop, $value)
    {
        $propName = $this->getPropertyName($obj, $prop);
        return '<input type="hidden" name="' . $propName . '" value="' . $value . '">';
    }

    private function getPropertyName($obj, $prop)
    {
        $modelName = ReflectionHelper::getInstance()->getObjectName($obj);
        return $modelName . "[" . $prop . "]";
    }

    public function getSubmit($customText = "submit")
    {
        return '<input type="submit" value="' . $customText . '">';
    }

    public function getText($text = "")
    {
        return '<p>' . $text . '</p>';
    }

    public function getLinkText($link, $text = "", $title = "", $target = "_self")
    {
        if ($text == "")
            $text = $link;
        if ($title == "")
            $title = $text;

        $link = RouteService::getInstance()->getAbsoluteLink($link);
        return $this->getText('<a href="' . $link . '" target="' . $target . '" title="' . $title . '">' . $text . '</a>');
    }

    public function getFormStart($action = null, $ajax = true)
    {
        if ($action == null)
            $action = RuntimeService::getInstance()->getTotalUrl();
        else
            $action = RouteService::getInstance()->getAbsoluteLink($action);

        $classes = "";
        if (!$ajax)
            $classes .= 'class="no-ajax"';

        return '
<form ' . $classes . ' action="' . $action . '" method="post">' . $this->getFormToken($action);
    }

    private function getFormToken($action)
    {
        $params = explode("/", $action);
        if ($params[count($params) - 1] == "create")
            return $this->getHiddenKeyValue("create", "true");

        $allowed = array("update", "delete");

        if (is_numeric($params[count($params) - 1]) || PasswordHelper::getInstance()->checkIfHashIsValid($params[count($params) - 1])) {
            if (in_array($params[count($params) - 2], $allowed)) {
                return $this->getHiddenKeyValue($params[count($params) - 2], "true");
            }
        } else {
            return $this->getHiddenKeyValue($params[count($params) - 1], "true");
        }
        return "";
    }

    /**
     * @param boolean $includeSubmit
     * @return string
     */
    public function getFormEnd($includeSubmit = true)
    {
        $output = "</form>";

        if ($includeSubmit)
            $output = $this->getSubmit() . $output;
        return $output;
    }

    public function getClassForMainMenuItem(array $routeParams, array $totalParams)
    {
        for ($i = 0; $i < count($routeParams); $i++) {
            if (!isset($totalParams) || $totalParams[$i] != $routeParams[$i])
                return "";
        }
        return ' class="active active-page"';
    }

    public function getClassesForMenuSubItem(array $controllerParams, $menuUrl)
    {
        $params = explode("/", $menuUrl);
        return $this->getClassForMainMenuItem($controllerParams, $params);
    }

    public function editDatabaseProperties(BaseDatabaseModel $model, ViewBase $view, $excludedProps = null)
    {
        $html = "";
        $props = $model->getDatabaseArray();
        foreach ($props as $key => $val) {
            if (in_array($key, $excludedProps))
                continue;

            if (str_ends_with($key, "Id") && strlen($key) > 2) {
                $objName = substr($key, 0, -2);
                $arrName = $objName . "s";
                $arr = $view->tryRetrieve($arrName);
                if (is_array($arr)) {
                    $html .= $this->getInput($model, $key, $objName, "select", "choose a " . $objName, $arr);
                }
            } else {
                $type = "text";
                $equals = array("Id");
                $starts = array("Is");
                $ends = array("Text",
                    "Email",
                    "Password",
                    "AuthHash",
                    "DateTime",
                    "Time",
                    "Date");

                $equalAssign = array("Id" => "hidden");
                $startAssign = array("Is" => "checkbox");
                $endAssign = array(
                    "Text" => "text",
                    "Email" => "email",
                    "Password" => "password",
                    "AuthHash" => "hidden",
                    "DateTime" => "datetime",
                    "Time" => "time",
                    "Date" => "date");

                $equalVal = str_equals_with_any($key, $equals, true);
                $startVal = str_starts_with_any($key, $starts, true);
                $endVal = str_ends_with_any($key, $ends, true);

                if ($equalAssign !== false)
                    $type = $equalAssign[$equalVal];
                else if ($startVal !== false)
                    $type = $startAssign[$startVal];
                else if ($endVal !== false)
                    $type = $endAssign[$endVal];

                $html .= $this->getInput($model, $key, null, $type);
            }
        }
        return $html;
    }

    public function readDatabaseProperties(BaseDatabaseModel $model, ViewBase $view, $excludedProps = null)
    {
        $html = "";
        $props = $model->getDatabaseArray();
        foreach ($props as $key => $val) {
            if (in_array($key, $excludedProps))
                continue;

            if (str_ends_with($key, "Id") && strlen($key) > 2) {
                $objName = substr($key, 0, -2);
                $obj = $view->tryRetrieve($objName);
                if ($obj instanceof BaseModel) {
                    $html .= "<p><b>" . $objName . "</b>" . $obj->getIdentification() . "</p>";
                }
            } else {
                $equalHides = array("Id" => "hidden");
                $endHides = array("Password", "AuthHash");

                $equalVal = str_equals_with_any($key, $equalHides, true);
                $endVal = str_ends_with_any($key, $endHides, true);

                if ($equalVal === false && $endVal === false) {
                    $html .= "<p><b>" . $key . "</b>" . $this->formatProperty($val, $key) . "</p>";
                }
            }
        }
        return $html;
    }

    public function getPart($const)
    {
        if ($const == PartHelper::PART_HEAD)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/head.php";
        if ($const == PartHelper::PART_FOOTER_CONTENT)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/footer_content.php";
        if ($const == PartHelper::PART_FOOTER_CRUD)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/footer_crud.php";
        if ($const == PartHelper::PART_HEADER_CENTER)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/header_center.php";
        if ($const == PartHelper::PART_HEADER_CONTENT)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/header_content.php";
        if ($const == PartHelper::PART_HEADER_CRUD)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/header_crud.php";
        if ($const == PartHelper::PART_LOADING_PLACEHOLDER)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/loading_placeholder.php";
        if ($const == PartHelper::PART_MENU)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/menu.php";
        if ($const == PartHelper::PART_MESSAGES)
            return RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/_parts/messages.php";

        LogHelper::getInstance()->logError("Part not found with const " . $const);
        return PartHelper::getPart(PartHelper::PART_MESSAGES);
    }

    public function getLogClass(LogItem $log)
    {
        $logType = LogHelper::getInstance()->convertToLogType($log->getLogLevel());
        if ($logType == LogHelper::LOG_TYPE_USER_ERROR)
            return "warning-message";
        if ($logType == LogHelper::LOG_TYPE_USER_INFO)
            return "info-message";
        if ($logType == LogHelper::LOG_TYPE_SYSTEM_ERROR)
            return "danger-message";
        LogHelper::getInstance()->logError("Unknown logtype", $log);
        return "";
    }

    public function getLogText(LogItem $log)
    {
        return LogHelper::getInstance()->renderLogItemAsHtml($log);
    }
}