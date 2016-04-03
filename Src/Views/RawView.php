<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 09.09.2015
 * Time: 23:44
 */
namespace famoser\phpFrame\Views;

class RawView extends ViewBase
{
    protected $content;

    public function __construct($content, $title = null, $description = null)
    {
        parent::__construct($title == null ? $this->getApplicationTitle() : $title, $description);
        $this->content = $content;
    }

    public function renderView()
    {
        return $this->content;
    }
}