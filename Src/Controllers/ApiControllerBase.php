<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 29.01.2016
 * Time: 13:09
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Views\MessageView;
use famoser\phpFrame\Views\RawView;

abstract class ApiControllerBase extends ControllerBase
{
    public function Display()
    {
        if (count($this->params) > 0){
            if ($this->params[0] == "log") {
                if (isset($this->request["message"]) && isset($this->request["loglevel"])) {
                    if ($this->request["loglevel"] == 0) {
                        LogHelper::getInstance()->logUserInfo($this->request["message"]);
                    } else if ($this->request["loglevel"] == 1) {
                        LogHelper::getInstance()->logUserError($this->request["message"]);
                    } else {
                        LogHelper::getInstance()->logError($this->request["message"]);
                    }
                }
                return $this->returnView(new RawView(PartHelper::PART_MESSAGES));
            }
        }
        return parent::Display();
    }

}