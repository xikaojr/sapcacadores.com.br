<?php

class App_Helper_FormataDatetime extends App_Helper_Abstract {

	public function formataDatetime($datetime) {

		$date = new Zend_Date($datetime);

		return $date->toString("d/m/Y H:i:s");
	}

}