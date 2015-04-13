<?php

class Zend_View_Helper_HelperUsuario extends Zend_View_Helper_Abstract {

    public function helperUsuario($id) {
        $usuarios = new Usurios();
        return (object) $usuarios->getUser($id);
    }

}
