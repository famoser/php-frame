<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 20:48
 */

namespace famoser\phpFrame\Views\Common\Elements;


use famoser\phpFrame\Views\Common\Elements\Base\ElementCollection;
use famoser\phpFrame\Views\Common\Elements\Base\Icon;
use famoser\phpFrame\Views\Common\Nodes\A;
use famoser\phpFrame\Views\Common\Nodes\Span;

class IconLink extends ElementCollection {
	
	public function __construct($link, $text, $icon, $showText = true) {
		$a = new A($link, null, $text, null, false);
		$a->addChildren(new Icon($icon));
		if ($showText) {
			$a->addChildren(new Span($text));
		}
		$this->addChildren($a);
	}
}