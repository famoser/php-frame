<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 20.12.2015
 * Time: 18:51
 */

namespace famoser\phpFrame\Views\Common\Nodes;


use famoser\phpFrame\Views\Common\Nodes\Base\Container;

class Section extends Container
{
    public function __construct($id, $customNode = "section")
    {
        parent::__construct($customNode);
        $this->setId($id);
    }
}