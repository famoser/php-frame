<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 20:45
 */

namespace famoser\phpFrame\Views\Common\Nodes;


use famoser\phpFrame\Views\Common\Nodes\Base\TextNode;

class I extends TextNode {
	public function __construct($text)
	{
		parent::__construct("i", $text);
	}
}