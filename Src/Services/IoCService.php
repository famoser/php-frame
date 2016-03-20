<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 17:11
 */

namespace famoser\phpFrame\Services;


class IoCService extends ServiceBase
{
    private $authenticationService;

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @param AuthenticationService $authenticationService
     */
    public function setAuthenticationService($authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }
}