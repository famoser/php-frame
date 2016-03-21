<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 21:03
 */

namespace famoser\phpFrame\Views\Common\Elements\Base;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class ElementCollection implements IHtmlElement {
	/* @var IHtmlElement[] */
	private $elements = array();
	
	/**
	 * @param IHtmlElement[]|IHtmlElement $children
	 */
	public function addChildren($children) {
		if (is_array($children)) {
			foreach ($children as $child) {
				$this->addChildren($child);
			}
		}
		else {
			if ($children instanceof IHtmlElement) {
				$this->elements[] = $children;
			}
		}
	}
	
	public function getHtml() {
		$output = "";
		foreach ($this->elements as $element) {
			$output .= $element->getHtml();
		}
		return $output;
	}
}