<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 21:44
 */

namespace famoser\phpFrame\Models\Services\GenericDatabaseService;


use famoser\phpFrame\Helpers\FormatHelper;

class InputPropertyModel extends TablePropertyModel implements \JsonSerializable
{
    private $inputType;
    private $inputName;

    public function setConfig($config)
    {
        $res = parent::setConfig($config);
        if (isset($config["InputType"])) {
            $this->inputType = $config["InputType"];
        }
        if (isset($config["InputName"]))
            $this->inputName = $config["InputName"];

        return $res;
    }


    /**
     * @return mixed
     */
    public function getInputType()
    {
        if (!is_null($this->inputType))
            return $this->inputType;
        else {
            if ($this->getType() == TablePropertyModel::TYPE_INTEGER ||
                $this->getType() == TablePropertyModel::TYPE_DOUBLE
            )
                return "number";
            else if ($this->getType() == TablePropertyModel::TYPE_BOOLEAN)
                return "checkbox";
            else if ($this->getType() == TablePropertyModel::TYPE_DATE)
                return "date";
            else if ($this->getType() == TablePropertyModel::TYPE_DATETIME)
                return "datetime";
            else if ($this->getType() == TablePropertyModel::TYPE_TIME)
                return "time";
            else if ($this->getType() == TablePropertyModel::TYPE_N1_RELATION)
                return "select";
            else if ($this->getType() == TablePropertyModel::TYPE_1N_RELATION)
                return "hidden";
            return "text";
        }
    }

    /**
     * @return mixed
     */
    public function getInputName()
    {
        if (!is_null($this->inputName))
            return $this->inputName;
        return $this->getDatabaseName();
    }

    public function getInputValue($value)
    {
        if (TablePropertyModel::TYPE_TEXT == $this->getType())
            return $value;
        else if (TablePropertyModel::TYPE_INTEGER == $this->getType())
            return is_numeric($value) ? $value : "";
        else if (TablePropertyModel::TYPE_DOUBLE == $this->getType())
            return is_numeric($value) ? $value : "";
        else if (TablePropertyModel::TYPE_BOOLEAN == $this->getType()) {
            if (is_bool($value))
                return $value;
            $parsedVal = strtolower($value);
            return $parsedVal == "true" || $value == 1 ? true : false;
        } else if (TablePropertyModel::TYPE_DATE == $this->getType())
            return FormatHelper::getInstance()->dateInput($value);
        else if (TablePropertyModel::TYPE_DATETIME == $this->getType())
            return FormatHelper::getInstance()->dateTimeInput($value);
        else if (TablePropertyModel::TYPE_TIME == $this->getType())
            return FormatHelper::getInstance()->timeInput($value);
        else if (TablePropertyModel::TYPE_N1_RELATION == $this->getType())
            return is_numeric($value) ? $value : 0;
        return null;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();
        $vars = get_object_vars($this);
        return array_merge($vars, $arr);
    }
}