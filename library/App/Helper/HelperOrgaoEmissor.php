<?php
class Zend_View_Helper_HelperOrgaoEmissor extends Zend_View_Helper_Abstract
{
    // @TODO - Pegar os orgao de acordo com o tipo e cliente
    // O Cliente poderá definir quais orgãos serão mostrados em cada tipo de 
    // apresentação
    
    public function helperOrgaoEmissor(){
        return $this;
    }
    
    public function getRg() {
        $class = new ConselhoOrgaoEmissor();
        return $class->fetchAll(null,'descricao')->toArray();
    }
    
    public function getConselho($where = null) {
        $class = new ConselhoOrgaoEmissor();
        
        $return = $class->buscar($where);
        
        if (count($return) == 0) {
            $return = $class->buscar();
        }
        
        return $return;
    }
}