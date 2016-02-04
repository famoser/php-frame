<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22.01.2016
 * Time: 01:18
 */

namespace Famoser\phpSLWrapper\Tests\Models;


use Famoser\phpSLWrapper\Framework\Models\DataService\EntityBase;

class TestModel extends EntityBase
{
    /* @database int primary notnull autoincrement */
    private $property1;

    /* @database text */
    private $text;

    public function __construct($text)
    {
        parent::__construct();
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }
}