<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 30.12.2015
 * Time: 13:33
 */

namespace famoser\phpFrame\Views\Common\Nodes;



use famoser\phpFrame\Views\Common\Nodes\Base\BaseNode;

class Button extends BaseNode
{
    public function __construct($text, $id = null)
    {
        parent::__construct("button", $id);
        $this->setText($text);
    }
}