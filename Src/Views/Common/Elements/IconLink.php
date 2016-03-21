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

class IconLink extends ElementCollection {
	
	public function __construct($link, $text, $icon) {
		$this->addChildren(new A($link, $text, $text, null));
		$this->addChildren(new Icon($icon));
	}
}