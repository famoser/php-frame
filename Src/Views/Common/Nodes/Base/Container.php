<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 26.12.2015
 * Time: 13:17
 */

namespace famoser\phpFrame\Views\Common\Nodes\Base;


use famoser\phpFrame\Views\Common\Nodes\Div;
use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;

class Container extends BaseNode {
	private $container;
	
	/**
	 * @param IHtmlElement[] $nodes
	 * @param int            $width       (1 -12)
	 * @param bool           $autoAdjust
	 * @param int|bool       $paddingLeft (1- 12)
	 * @param int|bool       $mdWidth
	 */
	public function addContent(array $nodes, $width = 12, $autoAdjust = true, $paddingLeft = false, $mdWidth = false) {
		//todo
		if ($this->container == null) {
			$div = new div();
			//$div->addClasses("container");
			$this->addChildren($div);
			$this->container = $div;
		}
		if ($mdWidth === false) {
			$mdWidth = $width;
			if ($autoAdjust) {
				$mdWidth = 2 * $mdWidth;
				if ($mdWidth > 12) {
					$mdWidth = 12;
				}
			}
		}
		
		$div = new div();
		/*$div->addClass("col-lg-" . $width);
		$div->addClass("col-md-" . $mdWidth);*/
		if (is_numeric($paddingLeft)) {
			//$div->addClass("col-lg-offset-" . $paddingLeft);
		}
		
		$div->addChildren($nodes);
		$this->container->addChildren($div);
	}
}