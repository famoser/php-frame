<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 30.12.2015
 * Time: 13:18
 */

namespace famoser\phpFrame\Views\Common\Nodes;


use famoser\phpFrame\Views\Common\Nodes\Base\Container;

class Form extends Container
{
    private $submitText;

    public function __construct($id, $action, $submitText = "senden", $method = "post")
    {
        parent::__construct("form", $id);
        $this->submitText = $submitText;
        $this->setProperty("method", $method);
    }

    public function getHtml()
    {
        $button = new Button($this->submitText);
        $this->addContent($button);
        return parent::getHtml();
    }
}