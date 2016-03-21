<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/03/2016
 * Time: 00:17
 */

namespace famoser\phpFrame\Views\Dashboard\Contents;


use famoser\phpFrame\Views\Common\Nodes\Form;
use famoser\phpFrame\Views\Common\Nodes\Interfaces\IHtmlElement;
use famoser\phpFrame\Views\Dashboard\Contents\Interfaces\IEditable;

class FormContent implements IHtmlElement {
	
	private $element;
	private $form;
	private $editKey;
	
	/**
	 * FormContent constructor.
	 *
	 * @param IEditable $element
	 * @param Form      $form
	 * @param null      $editKey
	 */
	public function __construct(IEditable $element, Form $form, $editKey = null) {
		$this->element = $element;
		$this->form = $form;
		$this->editKey = $editKey;
	}
	
	public function getHtml() {
		// TODO: Implement getHtml() method.
	}
}