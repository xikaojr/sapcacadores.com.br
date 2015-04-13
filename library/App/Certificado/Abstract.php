<?php

abstract class App_Certificado_Abstract {

    protected $_translate = null;

    public function __construct() {

        //Iniciando a library de tradução
        $this->_translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
    }

    abstract function listar(array $params);

    abstract function gerar(array $params);
}
