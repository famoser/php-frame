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
use famoser\phpFrame\Views\ViewBase;

class DashboardView extends ViewBase {
	private $leftMenu;
	private $topBar;
	private $content;

	public function __construct($title, $description, LeftMenu $leftMenu = null, TopBar $topBar = null) {
		parent::__construct($title, $description);
		$this->leftMenu = $leftMenu;
		$this->topBar = $topBar;
	}

	public function setContent(IHtmlElement $content)
	{
		$this->content = $content;
	}

	public function setLeftMenu(LeftMenu $leftMenu)
	{
		$this->leftMenu = $leftMenu;
	}

	public function setTopBar(TopBar $topBar)
	{
		$this->topBar = $topBar;
	}

	public function renderView()
	{
		// TODO: Implement renderView() method.
	}
}