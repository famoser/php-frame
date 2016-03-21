<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 20.12.2015
 * Time: 18:59
 */


namespace famoser\phpFrame\Views\Common\Nodes\Base;

use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class BaseNode extends TextNode {
	private $id;
	
	private $classes = array();
	
	private $children = array();
	private $properties = array();
	private $attributes = array();
	
	public function __construct($node, $id = null) {
		parent::__construct($node);
		if ($id != null) {
			$this->setId($id);
		}
	}
	
	/**
	 * @param string[]|string $classes
	 */
	public function addClasses($classes) {
		if (is_array($classes)) {
			foreach ($classes as $child) {
				$this->classes[] = $child;
			}
		}
		else {
			$this->classes[] = $classes;
		}
	}
	
	/**
	 * clears classes
	 */
	public function clearClasses() {
		$this->children[] = array();
	}
	
	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	public function clearId() {
		$this->id = null;
	}
	
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
				$this->children[] = $children;
			}
		}
	}
	
	public function clearChildren() {
		$this->children[] = array();
	}
	
	/**
	 * @param string $key
	 * @param string $value
	 */
	public function setProperty($key, $value) {
		$this->properties[$key] = $value;
	}
	
	/**
	 * @param string $attr
	 */
	public function setAttribute($attr) {
		$this->attributes[] = $attr;
	}
	
	/**
	 * @return string
	 */
	private function getClassPart() {
		$output = "";
		if (isset($this->classes)) {
			$output = implode(" ", $this->classes);
		}
		if ($output != "") {
			return 'class="' . $output . '"';
		}
		return "";
	}
	
	/**
	 * @return string
	 */
	private function getChildrenPart() {
		$output = "";
		foreach ($this->children as $child) {
			$output .= $child->getHtml();
		}
		return $output;
	}
	
	public function getIdPart() {
		if ($this->id != null) {
			return 'id="' . $this->id . '"';
		}
		return "";
	}
	
	/**
	 * @return string
	 */
	private function getPropertiesPart() {
		$output = "";
		foreach ($this->properties as $key => $val) {
			$output .= " " . $key . '="' . $val . '"';
		}
		return $output;
	}
	
	/**
	 * @return string
	 */
	private function getAttributesPart() {
		$output = "";
		foreach ($this->attributes as $attr) {
			$output .= " " . $attr;
		}
		return $output;
	}
	
	public function getHtml() {
		$nodeContent = $this->getNode();
		$id = $this->getIdPart();
		if ($id != "") {
			$nodeContent .= " " . $id;
		}
		$class = $this->getClassPart();
		if ($class != "") {
			$nodeContent .= " " . $class;
		}
		$attributes = $this->getAttributesPart();
		if ($attributes != "") {
			$nodeContent .= " " . $attributes;
		}
		$properties = $this->getPropertiesPart();
		if ($attributes != "") {
			$nodeContent .= " " . $properties;
		}
		
		$start = '<' . $nodeContent . '>';
		if ($this->getText() != "") {
			$start .= $this->getText();
		}
		
		$childrenHtml = $this->getChildrenPart();
		if ($childrenHtml != "") {
			$start .= $childrenHtml;
		}
		return $start . '</' . $this->getNode() . '>';
	}
}