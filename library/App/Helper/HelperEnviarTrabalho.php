<?php

class Zend_View_Helper_HelperEnviarTrabalho extends Zend_View_Helper_Abstract {

    public function helperEnviarTrabalho($centroCustoId) {
        $centrosCustosFasesTable = new CentrosCustosFases();
        $exibir = count($centrosCustosFasesTable->existePeriodoEnvio($centroCustoId));

        if ($exibir > 0)
            return true;

        return false;
    }

}