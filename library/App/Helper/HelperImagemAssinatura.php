<?php
class Zend_View_Helper_HelperImagemAssinatura extends Zend_View_Helper_Abstract
{
    public function helperImagemAssinatura ($extra = "P")
    {
        $imagem = "";
        
        if( is_file(PUBLIC_PATH . "images/clientes/". CLIENTE ."/assinatura.png") ) {
            $imagem = "/images/clientes/". CLIENTE ."/assinatura.png";
        }
        if( is_file(PUBLIC_PATH . "images/clientes/". CLIENTE ."/assinatura.jpg") ) {
            $imagem = "/images/clientes/". CLIENTE ."/assinatura.jpg";
        }

        return $imagem;
    }
}