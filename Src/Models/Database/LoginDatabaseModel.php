<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.02.2016
 * Time: 16:37
 */

namespace famoser\phpFrame\Models\Database;


abstract class LoginDatabaseModel extends BasePersonalDatabaseModel
{
    private $Email;
    private $PasswordHash;
    private $AuthHash;

    private $Password;
    private $ConfirmPassword;

    /**
     * @return array
     */
    public function getDatabaseArray()
    {
        $props = array("Email" => $this->getEmail(), "PasswordHash" => $this->getPasswordHash(), "AuthHash" => $this->getAuthHash());
        return array_merge($props, parent::getDatabaseArray());
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param string $Email
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->PasswordHash;
    }

    /**
     * @param string $PasswordHash
     */
    public function setPasswordHash($PasswordHash)
    {
        $this->PasswordHash = $PasswordHash;
    }

    /**
     * @return string
     */
    public function getAuthHash()
    {
        return $this->AuthHash;
    }

    /**
     * @param string $AuthHash
     */
    public function setAuthHash($AuthHash)
    {
        $this->AuthHash = $AuthHash;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * @param string $Password
     */
    public function setPassword($Password)
    {
        $this->Password = $Password;
    }

    /**
     * @return string
     */
    public function getConfirmPassword()
    {
        return $this->ConfirmPassword;
    }

    /**
     * @param string $ConfirmPassword
     */
    public function setConfirmPassword($ConfirmPassword)
    {
        $this->ConfirmPassword = $ConfirmPassword;
    }
}