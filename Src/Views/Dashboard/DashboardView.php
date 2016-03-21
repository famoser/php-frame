<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 21/03/2016
 * Time: 19:07
 */

namespace famoser\phpFrame\Views\Dashboard;


use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;
use famoser\phpFrame\Views\Dashboard\Items\LeftMenu\LeftMenu;
use famoser\phpFrame\Views\Dashboard\Items\LeftMenu\TopBar;

class DashboardView {
	private $leftMenu;
	private $topBar;
	private $content;

	public function __construct(LeftMenu $leftMenu, TopBar $topBar) {
		$this->leftMenu = $leftMenu;
		$this->topBar = $topBar;
	}
	
	public function setContent(IHtmlElement $content)
	{
		$this->content = $content;
	}
}