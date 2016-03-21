<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 22.12.2015
 * Time: 12:14
 */

namespace famoser\phpFrame\Views\Common\Nodes;



use famoser\phpFrame\Views\Common\Nodes\Base\BaseNode;

class Div extends BaseNode
{
    public function __construct($id = null)
    {
        parent::__construct("div", $id);
    }
}