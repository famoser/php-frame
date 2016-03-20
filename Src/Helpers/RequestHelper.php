<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 10.02.2016
 * Time: 11:19
 */

namespace famoser\phpFrame\Helpers;


class RequestHelper extends HelperBase
{
    public function isAjaxRequest()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
            return true;
        return false;
    }
}