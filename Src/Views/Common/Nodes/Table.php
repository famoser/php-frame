<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 25.12.2015
 * Time: 20:24
 */

namespace famoser\phpFrame\Views\Common\Nodes;


use famoser\phpFrame\Views\Common\Nodes\Base\BaseNode;

class Table extends BaseNode {
	/* @var BaseNode */
	private $body;
	
	/* @var BaseNode */
	private $head;
	
	public function __construct($id = null, $hover = false) {
		parent::__construct("table", $id);
		$this->addClasses("table");
		if ($hover) {
			$this->addClasses("table-hover");
		}
	}
	
	public function addRow(array $content) {
		$this->addBodyIfNeeded();
		
		$row = new basenode("tr");
		foreach ($content as $item) {
			$rowContent = new basenode("td");
			$rowContent->setText($item);
			$row->addChildren($rowContent);
		}
		if ($this->body instanceof BaseNode) {
			$this->body->addChildren($row);
		}
	}
	
	public function addRows(array $rows) {
		foreach ($rows as $row) {
			$this->addRow($row);
		}
	}
	
	public function addHeader(array $header) {
		$this->addHead();
		
		$row = new BaseNode("th");
		foreach ($header as $item) {
			$rowContent = new basenode("td");
			$rowContent->setText($item);
			$row->addChildren($rowContent);
		}
		$this->head->addChildren($row);
	}
	
	private function addHead() {
		$this->head = new BaseNode("thead");
		if ($this->body != null) {
			$this->clearChildren();
			$this->addChildren($this->head);
			$this->addChildren($this->body);
		}
		else {
			$this->addChildren($this->head);
		}
	}
	
	private function addBodyIfNeeded() {
		if ($this->body == null) {
			$this->body = new basenode("tbody");
			$this->addChildren($this->body);
		}
	}
}