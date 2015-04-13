<?php

class Zend_View_Helper_HelperCentrosCustos extends Zend_View_Helper_Abstract {

    public function helperCentrosCustos() {
        $classCentrosCustos = new CentrosCustos();
        return $classCentrosCustos->fetchAll(null, 'descricao')->toArray();
    }

}