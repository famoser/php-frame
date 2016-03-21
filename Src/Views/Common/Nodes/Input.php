<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 27.12.2015
 * Time: 22:13
 */

namespace famoser\phpFrame\Views\Common\Nodes;



use famoser\phpFrame\Views\Common\Nodes\Base\BaseNode;

class Input extends BaseNode
{
    public function __construct($type, $id, $placeholder, $required = true)
    {
        parent::__construct("input", $id);
        $this->setProperty("placeholder", $placeholder);
        $this->setProperty("type", $type);

        if ($required)
            $this->setAttribute("required");
    }
}