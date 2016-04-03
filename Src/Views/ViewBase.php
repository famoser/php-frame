<?php

/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 23.05.2015
 * Time: 14:14
 */
namespace famoser\phpFrame\Views;

use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\OutputHelper;
use famoser\phpFrame\Models\Controllers\ControllerConfigModel;
use famoser\phpFrame\Models\View\IconMenuItem;
use famoser\phpFrame\Models\View\MenuItem;

abstract class ViewBase
{
    /**
     * Enthält die Variablen, die in das Template eingebettet
     * werden sollen.
     */
    private $pageTitle;
    private $pageDescription;

    private $applicationTitle;
    private $applicationUrl;
    private $applicationAuthor;
    private $applicationAuthorUrl;

    private $keyValues = array();

    public function __construct($title, $description)
    {
        $this->pageTitle = $title;
        $this->pageDescription = $description;
    }

    public function setApplicationValues($applicationTitle, $applicationUrl, $applicationAuthor, $applicationAuthorUrl)
    {
        $this->applicationTitle = $applicationTitle;
        $this->applicationUrl = $applicationUrl;
        $this->applicationAuthor = $applicationAuthor;
        $this->applicationAuthorUrl = $applicationAuthorUrl;
    }


    /**
     * Ordnet eine Variable einem bestimmten Schlüssel zu.
     *
     * @param String $key Schlüssel
     * @param String $value Variable
     */
    public function assign($key, $value)
    {
        $this->keyValues[$key] = $value;
    }

    public function retrieve($key)
    {
        if (isset($this->keyValues[$key])) {
            return $this->keyValues[$key];
        }
        LogHelper::getInstance()->logError("item not found in view: " . $key);
        return null;
    }

    public function tryRetrieve($key)
    {
        if (isset($this->keyValues[$key])) {
            return $this->keyValues[$key];
        }
        return null;
    }

    /**
     * @return null
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param null $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return null
     */
    public function getPageDescription()
    {
        return $this->pageDescription;
    }

    /**
     * @param null $pageDescription
     */
    public function setPageDescription($pageDescription)
    {
        $this->pageDescription = $pageDescription;
    }

    /**
     * @return mixed
     */
    public function getApplicationTitle()
    {
        return $this->applicationTitle;
    }

    /**
     * @return mixed
     */
    public function getApplicationUrl()
    {
        return $this->applicationUrl;
    }

    /**
     * @return mixed
     */
    public function getApplicationAuthor()
    {
        return $this->applicationAuthor;
    }

    /**
     * @return mixed
     */
    public function getApplicationAuthorUrl()
    {
        return $this->applicationAuthorUrl;
    }

    public function includeFile($file)
    {
        if (file_exists($file)) {
            include $file;
        } else {
            LogHelper::getInstance()->logError("file does not exist: " . $file);
        }

    }

    abstract public function renderView();
}