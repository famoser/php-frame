<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.02.2016
 * Time: 14:35
 */

namespace famoser\phpFrame\Services;


use famoser\phpFrame\Models\Database\LoginDatabaseModel;

class AuthenticationService extends ServiceBase
{
    public function __construct()
    {
        parent::__construct(false);
    }

    /**
     * @return LoginDatabaseModel|bool
     */
    public function getUser()
    {
        if (isset($_SESSION["user"]))
            return unserialize($_SESSION["user"]);
        return false;
    }

    /**
     * @param LoginDatabaseModel $user
     */
    public function setUser(LoginDatabaseModel $user)
    {
        if ($user == null)
            unset($_SESSION["user"]);
        else
            $_SESSION["user"] = serialize($user);
    }
}