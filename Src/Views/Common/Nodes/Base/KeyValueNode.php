<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 22.12.2015
 * Time: 22:43
 */

namespace famoser\phpFrame\Views\Common\Nodes\Base;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class KeyValueNode implements IHtmlElement
{
    private $node;
    private $keyValues = array();

    public function __construct($node)
    {
        $this->node = $node;
    }

    public function addKeyValue($key, $value)
    {
        $this->keyValues[$key] = $value;
    }

    public function getHtml()
    {
        $output = "<" . $this->node . " ";
        foreach ($this->keyValues as $key => $val) {
            $output .= $key . '="' . $val . '" ';
        }
        if ($this->node == "link" || $this->node == "meta" || $this->node == "base" || $this->node == "br")
            $output .= "/>";
        else
            $output .= "></" . $this->node . ">";
        return $output;
    }
}