<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 21:32
 */

namespace famoser\phpFrame\Views\Common\Nodes;


use famoser\phpFrame\Views\Common\Nodes\Base\TextNode;

class Span extends TextNode {
	public function __construct($text) {
		parent::__construct("span", $text);
	}
}