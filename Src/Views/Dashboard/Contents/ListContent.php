<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 23:54
 */

namespace famoser\phpFrame\Views\Dashboard\Contents;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;
use famoser\phpFrame\Views\Dashboard\Contents\Interfaces\IListEnumerable;

class ListContent implements IHtmlElement {
	private $elements = array();
	private $templateKey;
	
	/**
	 * ListContent constructor.
	 *
	 * @param IListEnumerable[] $elements
	 * @param null  $templateKey
	 */
	public function __construct(array $elements, $templateKey = null) {
		$this->elements = $elements;
		$this->templateKey = $templateKey;
	}
	
	public function getHtml() {
		$output = "";
		foreach ($this->elements as $element) {
			$output .= $element->getListView($this->templateKey);
		}
		return $output;
	}
}