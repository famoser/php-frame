<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 20.12.2015
 * Time: 18:59
 */


namespace famoser\phpFrame\Views\Common\Nodes\Base;

use famoser\phpFrame\Views\Common\Nodes\Interfaces\iHtmlElement;

class Text implements iHtmlElement
{
    private $text;

    public function __construct($text = null)
    {
        $this->text = $text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function addText($text)
    {
        $this->text .= $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getHtml()
    {
        return $this->text;
    }
}