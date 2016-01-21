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
    public function __construct()
    {
        $this->IsInDatabase = false;
    }

    private $IsInDatabase;

    /**
     * @return bool
     */
    public function IsInDatabase()
    {
        if (!$this->IsInDatabase)
            return $this->Id > 0;
        return true;
    }

    /**
     * @param bool $IsInDatabase
     */
    public function setIsInDatabase(bool $IsInDatabase)
    {
        $this->IsInDatabase = $IsInDatabase;
    }


    /* @database INT, notnull, autoincrement, primary */
    private $Id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param mixed $Id
     * @return EntityBase
     * @attr
     */
    public function setId($Id)
    {
        $this->Id = $Id;
        return $this;
    }
}