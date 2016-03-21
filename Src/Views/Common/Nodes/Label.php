<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 27.12.2015
 * Time: 21:56
 */

namespace famoser\phpFrame\Views\Common\Nodes;



use famoser\phpFrame\Views\Common\Nodes\Base\BaseNode;

class Label extends BaseNode
{
    public function __construct($for, $name = null)
    {
        parent::__construct("label");
        $this->setProperty("for", $for);
        if ($name == null)
            $name = $for;
        $this->setText($name);
    }
}