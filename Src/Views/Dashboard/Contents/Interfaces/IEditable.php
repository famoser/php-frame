<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/03/2016
 * Time: 00:19
 */

namespace famoser\phpFrame\Views\Dashboard\Contents\Interfaces;


interface IEditable {
	public function getEditView($key = null);
}