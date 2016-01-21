<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 18.01.2016
 * Time: 19:19
 */

namespace Famoser\phpSLWrapper\Framework\Models\DataService;


class EntityBase
{
    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function IsInDatabase()
    {
        return $this->id > 0;
    }


    /* @database INT, notnull, autoincrement, primary */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}