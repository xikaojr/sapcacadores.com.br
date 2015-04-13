<?php

class Zend_View_Helper_ClearMask extends Zend_View_Helper_Abstract {

	public function ClearMask($var) {
		return preg_replace("/[_\W]/", "", $var);
	}

}

