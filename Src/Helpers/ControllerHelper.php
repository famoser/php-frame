<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.03.2016
 * Time: 17:47
 */

namespace famoser\phpFrame\Helpers;


class ControllerHelper extends HelperBase
{
    public function isPostRequest(array $request, $value)
    {
        return is_array($request) && isset($request[$value]);
    }
}