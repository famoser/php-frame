<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.02.2016
 * Time: 14:33
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\PasswordHelper;
use famoser\phpFrame\Helpers\ReflectionHelper;
use famoser\phpFrame\Helpers\RequestHelper;
use famoser\phpFrame\Models\Database\LoginDatabaseModel;
use famoser\phpFrame\Services\AuthenticationService;
use famoser\phpFrame\Services\EmailService;
use famoser\phpFrame\Services\GenericDatabaseService;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Views\GenericCenterView;

abstract class LoginControllerBase extends ControllerBase
{
    private $instance;
    private $authService;
    private $loggedInRedirect;

    public function __construct($request, $params, $files, LoginDatabaseModel $implementation, AuthenticationService $authService, $loggedInRedirect)
    {
        parent::__construct($request, $params, $files);
        $this->instance = $implementation;
        $this->authService = $authService;
        $this->loggedInRedirect = $loggedInRedirect;
    }

    public function Display()
    {
        $user = $this->authService->getUser();
        if ($user !== false) {
            $this->exitWithControllerRedirect($this->loggedInRedirect);
        }

        if (count($this->params) == 0) {
            $view = new GenericCenterView("LoginController", "login", null, true);
            return $this->returnView($view);
        } else if (count($this->params) > 0) {
            if ($this->params[0] == "login") {
                if (isset($this->request["login"]) && $this->request["login"] == "true") {
                    //fill object
                    ReflectionHelper::getInstance()->writeFromPostArrayToObjectProperties($this->instance, $this->request);

                    $admin = GenericDatabaseService::getInstance()->getSingle($this->instance, array("Username" => $this->instance->getEmail()), true);
                    if ($admin instanceof LoginDatabaseModel && PasswordHelper::getInstance()->validatePasswort($this->instance->getPassword(), $admin->getPasswordHash())) {
                        AuthenticationService::getInstance()->setUser($admin);
                        $this->exitWithRedirect($this->loggedInRedirect);
                    } else {
                        LogHelper::getInstance()->logUserError("login unsuccessful!");
                        $this->instance->setPassword("");
                    }
                }

                $view = new GenericCenterView("LoginController", "login", null, true);
                $view->assign("model", $this->instance);
                return $this->returnView($view);
            } else if ($this->params[0] == "logout") {
                $this->authService->setUser(null);
                $this->exitWithControllerRedirect("/");
            } else {
                return parent::Display();
            }
        } else if (count($this->params) > 1) {
            if ($this->params[0] == "activateAccount" && PasswordHelper::getInstance()->checkIfHashIsValid($this->params[1])) {
                $admin = GenericDatabaseService::getInstance()->getSingle($this->instance, array("AuthHash" => $this->params[1]), true);
                if ($admin instanceof LoginDatabaseModel) {
                    if (isset($this->request["activateAccount"]) && $this->request["activateAccount"] == true) {
                        ReflectionHelper::getInstance()->writeFromPostArrayToObjectProperties($this->request, $admin);

                        if ($this->canSetPassword($admin)) {
                            $admin->setPasswordHash(PasswordHelper::getInstance()->convertToPasswordHash($admin->getPassword()));
                            $admin->setAuthHash("");
                            GenericDatabaseService::getInstance()->update($admin, array("Id", "AuthHash", "PasswordHash"));
                        }
                    }

                    $view = new GenericCenterView("LoginController", "addpass", null, true);
                    return $this->returnView($view);
                } else {
                    LogHelper::getInstance()->logUserInfo("link not valid anymore");
                    $view = new GenericCenterView("LoginController", "login", null, true);
                    return $this->returnView($view);
                }
            } else if ($this->params[0] == "forgotpass") {
                if (isset($this->request["forgotpass"]) && $this->request["forgotpass"] == "true") {

                    $newHash = PasswordHelper::getInstance()->createUniqueHash();
                    $admin = GenericDatabaseService::getInstance()->getSingle($this->instance, array("Username" => $this->request["Username"]));
                    if ($admin instanceof LoginDatabaseModel) {
                        $admin->setAuthHash($newHash);
                        GenericDatabaseService::getInstance()->update($admin, array("Id", "AuthHash"));
                        return EmailService::getInstance()->sendEmailFromServer(
                            LocaleService::getInstance()->translate("password reset"),
                            LocaleService::getInstance()->translate("your password was reset. click following link to set a new one: " . RuntimeService::getInstance()->getRouteUrl() . "/activateAccount/" . $newHash),
                            $admin->getAuthHash());
                    }
                    LogHelper::getInstance()->logUserInfo("you will be contacted by us per email.");
                }

                $view = new GenericCenterView("LoginController", "forgotpass", null, true);
                return $this->returnView($view);
            }
        }
        return parent::Display();
    }

    protected function isParamReserved()
    {
        if (count($this->params) > 0) {
            if ($this->params[0] == "login") {
                return true;
            }
            if ($this->params[0] == "logout") {
                return true;
            }
        } else if (count($this->params) > 1) {
            if ($this->params[0] == "activateAccount" && PasswordHelper::getInstance()->checkIfHashIsValid($this->params[1])) {
                return true;
            } else if ($this->params[0] == "forgotpass") {
                return true;
            }
        }
        return false;
    }

    private function canSetPassword(LoginDatabaseModel $model)
    {
        if ($model->getPassword() != $model->getConfirmPassword()) {
            LogHelper::getInstance()->logUserError("passwords do not match");
            return false;
        }

        $failure = PasswordHelper::getInstance()->checkPassword($model->getPassword());
        if ($failure !== true) {
            LogHelper::getInstance()->logUserError(PasswordHelper::getInstance()->evaluateFailure($failure));
            return false;
        }

        return true;
    }
}