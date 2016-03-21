<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 22.12.2015
 * Time: 18:02
 */

namespace famoser\phpFrame\Views\Common\Nodes;


use famoser\phpFrame\Views\Common\Nodes\Base\BaseNode;

class A extends BaseNode {
	private $link;
	private $alt;
	private $target;

	public function __construct($link, $text = null, $alt = null, $target = "_blank", $showText = true) {
		parent::__construct("a");

		$this->link = $link;

		if ($showText) {
			if ($text == null) {
				$this->setText($link);
			}
			else {
				$this->setText($text);
			}
		}

		if ($alt == null) {
			$this->alt = $text;
		}
		else {
			$this->alt = $alt;
		}

		$this->target = $target;
	}

	public function getHtml() {
		$target = "";
		if ($this->target != "") {
			$target = ' target="' . $this->target . '"';
		}
		$alt = "";
		if ($this->alt != "") {
			$alt = ' alt="' . $this->alt . '"';
		}
		return '<a href="' . $this->link . '"' . $alt . $target . '">' . $this->getText() . '</a>';
	}
}