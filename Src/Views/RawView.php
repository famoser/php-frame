<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 09.09.2015
 * Time: 23:44
 */
namespace famoser\phpFrame\Views;

use famoser\phpFrame\Helpers\PartHelper;

class RawView extends ViewBase
{
    protected $part;

    public function __construct($part)
    {
        parent::__construct();
        $this->part = $part;
    }

    public function loadTemplate()
    {
        return $this->loadFile(PartHelper::getInstance()->getPart($this->part));
    }
}