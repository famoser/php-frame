<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/03/2016
 * Time: 00:04
 */

namespace famoser\phpFrame\Views\Dashboard\Contents\Interfaces;


interface IListEnumerable {
	function getListView($templateKey = null);
}