<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 21:27
 */

namespace famoser\phpFrame\Views\Dashboard\Items\TopBar;


use famoser\phpFrame\Views\Common\Elements\IconLink;

class ShortCut extends IconLink {
	public function __construct($link, $text, $icon) {
		parent::__construct($link, $text, $icon);
	}
}