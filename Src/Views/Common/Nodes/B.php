<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 22.12.2015
 * Time: 18:02
 */

namespace famoser\phpFrame\Views\Common\Nodes;



use famoser\phpFrame\Views\Common\Nodes\Base\TextNode;

class B extends TextNode
{
    public function __construct($text)
    {
        parent::__construct("b", $text);
    }
}

