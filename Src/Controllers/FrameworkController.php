<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 19.02.2016
 * Time: 12:29
 */

namespace famoser\phpFrame\Controllers;


use famoser\phpFrame\Views\MessageView;

class FrameworkController extends ControllerBase
{
    const SHOW_MESSAGE = 1;
    const CONTROLLER_NOT_FOUND = 2;

    public function __construct()
    {
        parent::__construct(null, null, null);
    }

    public function Display($action = FrameworkController::SHOW_MESSAGE)
    {
        if ($action == FrameworkController::SHOW_MESSAGE) {
            $view = new MessageView(true);
            return $this->returnView($view);
        } else if ($action == FrameworkController::CONTROLLER_NOT_FOUND) {
            return $this->returnFailure(ControllerBase::FAILURE_NOT_FOUND);
        }
        return parent::Display();
    }
}