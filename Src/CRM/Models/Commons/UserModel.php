<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 19:38
 */

namespace Famoser\phpSLWrapper\CRM\Models\Commons;


use Famoser\phpSLWrapper\Framework\Models\DataService\EntityBase;

class UserModel extends EntityBase
{
    /* @database string, notnull */
    private $Name;

    public function __construct($name)
    {
        parent::__construct();
        $this->Name = $name;
    }
}