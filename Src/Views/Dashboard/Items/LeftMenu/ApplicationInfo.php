<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 21:20
 */

namespace famoser\phpFrame\Views\Dashboard\Items\LeftMenu;


class ApplicationInfo {
	private $name;
	private $logo;
	
	public function __construct($name, $logo = null) {
		$this->name = $name;
		$this->logo = $logo;
	}
}