<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 19:12
 */

namespace famoser\phpFrame\Views\Dashboard\Items\LeftMenu;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class LeftMenu {

	private $leftMenuTopItems = array();
	private $leftMenuSecondaryItems = array();

	private $info;

	public function __construct(ApplicationInfo $info) {
		$this->info = $info;
	}

	public function addLeftMenuItem(LeftMenuItem $item) {
		$this->leftMenuTopItems[] = $item;
	}

	public function addLeftMenuSecondaryItem(LeftMenuItem $item) {
		$this->leftMenuSecondaryItems[] = $item;
	}
}