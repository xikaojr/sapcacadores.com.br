<?php

class App_Helper_NomePost extends App_Helper_Abstract {

	public function nomePost($nome) {

		$nome = str_replace(" ", "-", trim($this->remove_accents($nome)));

		$clean = preg_replace('~(^[^a-z0-9]+)|([^a-z0-9]+$)~i', '', strtolower($nome));
		return $clean;
	}

}