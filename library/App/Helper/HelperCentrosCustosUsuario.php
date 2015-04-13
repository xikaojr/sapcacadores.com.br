<?php

class Zend_View_Helper_HelperCentrosCustosUsuario extends Zend_View_Helper_Abstract {

    public function helperCentrosCustosUsuario($identity) {
        $centroCustoUsuario = array();
        $usuarioCentroCusto = new UsuariosCentrosCustos();
        $rs = $usuarioCentroCusto->findAllByUsuarioId($identity->id);

        foreach ($rs as $r) {
            $centroCustoUsuario[] = $r['centros_custos_id'];
        }
        return $centroCustoUsuario;
    }

}