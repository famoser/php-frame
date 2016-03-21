<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/03/2016
 * Time: 00:06
 */

namespace famoser\phpFrame\Views\Dashboard\Contents;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class TabContent implements IHtmlElement {
	private $content;
	private $title;
	private $isSelected;
	
	public function __construct(IHtmlElement $content, $title, $isSelected = false) {
		$this->content = $content;
		$this->title = $title;
		$this->isSelected = $isSelected;
	}
	
	public function getHtml() {
		// TODO: Implement getHtml() method.
	}
}