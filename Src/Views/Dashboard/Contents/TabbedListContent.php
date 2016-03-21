<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/03/2016
 * Time: 00:06
 */

namespace famoser\phpFrame\Views\Dashboard\Contents;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class TabbedListContent implements IHtmlElement {
	private $tabs = array();

	public function __construct(array $tabs) {
		$this->tabs = $tabs;
	}
	
	public function getHtml() {
		// TODO: Implement getHtml() method.
	}
}