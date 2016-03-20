<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.02.2016
 * Time: 14:39
 */

namespace famoser\phpFrame\Interfaces\Helpers;


interface IHelper
{
    /**
     * @param $const
     * @return string
     */
    public function evaluateFailure($const);
}