<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 18:57
 */

namespace famoser\phpFrame\Views;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Services\SettingsService;

class GenericView extends ViewBase
{
    private $controller = null;
    private $view = null;
    private $folder = null;
    private $fromFramework = null;

    private $useCenter = false;

    public function __construct($controller, $view = "index", $folder = null, $fromFramework = false)
    {
        parent::__construct();
        $this->controller = $controller;
        $this->view = $view;
        $this->fromFramework = $fromFramework;
        if (strlen($folder) > 0)
            $this->folder = $folder . "/";
    }

    protected function useCenter($val)
    {
        $this->useCenter = $val;
    }

    public function loadTemplate()
    {
        $content = "";
        if ($this->fromFramework) {
            $filePath = RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/" . $this->controller . "/" . $this->folder . $this->view . ".php";
        } else {
            $filePath = RuntimeService::getInstance()->getTemplatesDirectory() . "/" . $this->controller . "/" . $this->folder . $this->view . ".php";
        }

        if (file_exists($filePath)) {
            $fileContent = $this->loadFile($filePath);
            if (trim($fileContent) == "")
                LogHelper::getInstance()->logError("output of file " . $filePath . "is empty");
            else
                $content .= $fileContent;
        } else
            LogHelper::getInstance()->logError("output file not found: " . $filePath);


        $const = PartHelper::PART_HEADER_CONTENT;
        if ($this->useCenter)
            $const = PartHelper::PART_HEADER_CENTER;

        $content = $this->loadFile(PartHelper::getInstance()->getPart($const)) . $content;

        $const = PartHelper::PART_FOOTER_CONTENT;
        if ($this->useCenter)
            $const = PartHelper::PART_FOOTER_CENTER;

        $content .= $this->loadFile(PartHelper::getInstance()->getPart($const));

        return $content;
    }
}