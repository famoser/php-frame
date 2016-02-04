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
    /* @database TEXT, notnull */
    private $name;

    public function __construct($name, $id = 0)
    {
        parent::__construct($id);
        $this->name = $name;
    }
}