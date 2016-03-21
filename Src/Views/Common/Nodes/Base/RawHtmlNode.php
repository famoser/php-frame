<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 23.12.2015
 * Time: 15:34
 */

namespace famoser\phpFrame\Views\Common\Nodes\Base;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\iHtmlElement;

class RawHtmlNode implements iHtmlElement
{
    private $html;

    public function __construct($filePath = null)
    {
        if ($filePath != null)
            $this->html = file_to_string($filePath);
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }
}