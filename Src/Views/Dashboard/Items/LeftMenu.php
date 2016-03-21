<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 19:12
 */

namespace famoser\phpFrame\Views\Dashboard\Items\LeftMenu;


class LeftMenu {

	private $leftMenuItem = array();

	public function addLeftMenuItem(LeftMenuItem $item)
	{
		$this->leftMenuItem[] = $item;
	}
}