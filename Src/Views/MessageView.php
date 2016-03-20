<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 09:36
 */

namespace famoser\phpFrame\Views;

use famoser\phpFrame\Helpers\PartHelper;

class MessageView extends ViewBase
{
    protected $showLink;

    public function __construct($showLink = true)
    {
        $this->showLink = $showLink;
        parent::__construct();
    }

    public function showLink()
    {
        return $this->showLink;
    }

    public function loadTemplate()
    {
        $content = $this->loadFile(PartHelper::getInstance()->getPart(PartHelper::PART_HEADER_CENTER));
        $content .= $this->loadFile(PartHelper::getInstance()->getPart(PartHelper::PART_MESSAGES));
        $content .= $this->loadFile(PartHelper::getInstance()->getPart(PartHelper::PART_FOOTER_CENTER));
        return $content;
    }
}