<?php
class Zend_View_Helper_HelperCodigoDoBrasil extends Zend_View_Helper_Abstract
{
    public function helperCodigoDoBrasil()
    {
        $config = new ConfiguracoesTable();
        $param = $config->findAllByCodigo(133);
        
        if (isset($param) && !empty($param)) {
            $codigo = $param['valor_referencia'];
        } else {
            $codigo = 12;
        }
        
        return $codigo;
    }
    
}