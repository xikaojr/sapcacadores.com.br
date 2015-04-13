<?php

class Zend_View_Helper_HelperConfiguracoes extends Zend_View_Helper_Abstract {

    public function helperConfiguracoes($codigo, $centroCustoId) {
        
        $configuracaoTable = new Configuracao();
        $config = $configuracaoTable->findAllByCodigoAndCentroCustoId($codigo, $centroCustoId);
        
        if (isset($config)) {
            return $config;
        } else {
            return array();
        }
        
    }
    
}