<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 11:03
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Services\SettingsService;
use famoser\phpFrame\Views\MessageView;
use famoser\phpFrame\Views\ViewBase;

abstract class ControllerBase
{
    protected $request;
    protected $params;
    protected $files;

    private $applicationConfig;

    const FAILURE_ACCESS_DENIED = 10;
    const FAILURE_NOT_FOUND = 11;
    const FAILURE_SERVER_ERROR = 12;

    const SUCCESS_CREATED = 20;
    const SUCCESS_UPDATED = 21;
    const SUCCESS_DELETED = 22;
    const SUCCESS_GENERAL = 23;

    const REDIRECTION_TEMPORARY = 20;
    const REDIRECTION_PERMANENTLY = 21;
    const REDIRECTION_ONE_TIME = 22;

    public function __construct($request, $params, $files)
    {
        $this->request = $request;
        $this->params = $params;
        $this->files = $files;

        $this->applicationConfig = SettingsService::getInstance()->getValueFor("Application");
    }

    public function Display()
    {
        return $this->returnFailure(ControllerBase::FAILURE_NOT_FOUND);
    }

    protected function returnFailure($code = ControllerBase::FAILURE_SERVER_ERROR, $message = "")
    {
        if ($message == "") {
            if ($code == ControllerBase::FAILURE_ACCESS_DENIED) {
                header("HTTP/1.1 401 Unauthorized");
                LogHelper::getInstance()->logUserError(LocaleService::getInstance()->getResources()->getKey("FAILURE_ACCESS_DENIED"));
            } else if ($code == ControllerBase::FAILURE_NOT_FOUND) {
                header("HTTP/1.0 404 Not Found");
                LogHelper::getInstance()->logError(LocaleService::getInstance()->getResources()->getKey("FAILURE_NOT_FOUND"));
            } else if ($code == ControllerBase::FAILURE_SERVER_ERROR) {
                header("HTTP/1.0 500 Internal Server Error");
                LogHelper::getInstance()->logError(LocaleService::getInstance()->getResources()->getKey("FAILURE_SERVER_ERROR"));
            } else {
                header("HTTP/1.0 500 Internal Server Error");
                LogHelper::getInstance()->logError("Unknown returnFailure const!");
            }
        }

        $view = new MessageView();
        return $this->returnView($view);
    }

    protected function returnSuccess($code = ControllerBase::SUCCESS_GENERAL, $message = "")
    {
        if ($message == "") {
            if ($code == ControllerBase::SUCCESS_CREATED)
                LogHelper::getInstance()->logUserInfo(LocaleService::getInstance()->getResources()->getKey("SUCCESS_CREATED"));
            else if ($code == ControllerBase::SUCCESS_UPDATED)
                LogHelper::getInstance()->logUserInfo(LocaleService::getInstance()->getResources()->getKey("SUCCESS_UPDATED"));
            else if ($code == ControllerBase::SUCCESS_DELETED)
                LogHelper::getInstance()->logUserInfo(LocaleService::getInstance()->getResources()->getKey("SUCCESS_DELETED"));
            else if ($code == ControllerBase::SUCCESS_GENERAL)
                LogHelper::getInstance()->logUserInfo(LocaleService::getInstance()->getResources()->getKey("SUCCESS_GENERAL"));
            else
                LogHelper::getInstance()->logError("Unknown returnSuccess const!");
        }

        $view = new MessageView();
        return $this->returnView($view);
    }

    protected function exitWithRedirect($url, $code = ControllerBase::REDIRECTION_ONE_TIME)
    {
        if ($code == ControllerBase::REDIRECTION_ONE_TIME)
            header("HTTP/1.0 302 Found");
        else if ($code == ControllerBase::REDIRECTION_TEMPORARY)
            header("HTTP/1.0 303 Temporary Redirect");
        else if ($code == ControllerBase::REDIRECTION_PERMANENTLY)
            header("HTTP/1.0 301 Moved Permanently");
        else {
            LogHelper::getInstance()->logError("Unknown returnRedirect const!");
            $this->exitWithRedirect($url);
            return;
        }

        header('Location: ' . $url);
        exit;
    }

    protected function exitWithControllerRedirect($relativeUrl)
    {
        $preUrl = $this->applicationConfig["Url"] . RuntimeService::getInstance()->getRouteUrl();
        header("HTTP/1.0 302 Found");
        header('Location: ' . $preUrl . "/" . $relativeUrl);
        exit;
    }

    protected function returnView(ViewBase $view)
    {
        $view->setDefaultValues($this->applicationConfig["Title"], $this->applicationConfig["Description"], $this->applicationConfig["Author"], $this->applicationConfig["AuthorUrl"]);
        $view->setApplicationValues($this->applicationConfig["Name"], $this->applicationConfig["Url"]);
        return $view->loadTemplate();
    }
}