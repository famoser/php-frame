<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 21:24
 */

namespace famoser\phpFrame\Views\Dashboard\Items\LeftMenu;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;
use famoser\phpFrame\Views\Dashboard\Items\TopBar\ShortCut;

class TopBar implements IHtmlElement {
	private $pageName;
	private $shortCuts = array();

	public function __construct($pageName, $showSearchBar = true) {
		$this->pageName = $pageName;
	}

	public function addShortCuts(ShortCut $shortCut) {
		$this->shortCuts[] = $shortCut;
	}
	
	public function getHtml() {
		// TODO: Implement getHtml() method.
	}
}