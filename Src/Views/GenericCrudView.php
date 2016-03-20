<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 18:50
 */

namespace famoser\phpFrame\Views;

use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Services\RuntimeService;

class GenericCrudView extends ViewBase
{
    private $controller;
    private $mode;
    private $fromFramework;

    /**
     * GenericCrudView constructor.
     * @param string $controller
     * @param string $mode
     * @param boolean $fromFramework
     */
    public function __construct(string $controller, string $mode, $fromFramework = false)
    {
        parent::__construct();
    }

    public function loadTemplate()
    {
        $content = $this->loadFile(PartHelper::getInstance()->getPart(PartHelper::PART_HEADER_CRUD));
        if ($this->fromFramework){
            $content .= $this->loadFile(RuntimeService::getInstance()->getFrameworkDirectory() . "/Templates/" . $this->controller . "/_crud/" . $this->mode . ".php");
        } else {
            $content .= $this->loadFile(RuntimeService::getInstance()->getTemplatesDirectory() . "/" . $this->controller . "/_crud/" .  $this->mode . ".php");
        }
        $content .= $this->loadFile(PartHelper::getInstance()->getPart(PartHelper::PART_FOOTER_CRUD));
        return $content;
    }
}