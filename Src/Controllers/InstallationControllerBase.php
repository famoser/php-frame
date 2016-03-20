<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 16:41
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\ControllerHelper;
use famoser\phpFrame\Services\GenericDatabaseService;
use famoser\phpFrame\Views\GenericCenterView;
use famoser\phpFrame\WorkFlows\SetupWorkFlow;

abstract class InstallationControllerBase extends ControllerBase
{
    public function Display()
    {
        if (count($this->params) == 0) {
        } else if (count($this->params) > 0) {
            if ($this->params[0] == "setup") {
                if (ControllerHelper::getInstance()->isPostRequest($this->request, "setup")) {
                    $exe = SetupWorkFlow::getInstance()->execute();
                    if (!$exe)
                        LogHelper::getInstance()->logUserError("setup failed!");
                }
                $view = new GenericCenterView("InstallationController", "setup", null, true);
                return $this->returnView($view);
            } else if ($this->params[0] == "refreshCss") {
                if (ControllerHelper::getInstance()->isPostRequest($this->request, "setup")) {
                    $exe = SetupWorkFlow::getInstance()->refreshCss();
                    if (!$exe)
                        LogHelper::getInstance()->logUserError("setup failed!");
                }
                $view = new GenericCenterView("InstallationController", "setup", null, true);
                return $this->returnView($view);
            } else if ($this->params[0] == "refreshJs") {
                if (ControllerHelper::getInstance()->isPostRequest($this->request, "setup")) {
                    $exe = SetupWorkFlow::getInstance()->refreshJs();
                    if (!$exe)
                        LogHelper::getInstance()->logUserError("setup failed!");
                }
                $view = new GenericCenterView("InstallationController", "setup", null, true);
                return $this->returnView($view);
            }
        }
        return parent::Display();
    }
}