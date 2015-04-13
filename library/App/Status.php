<?php

/**
 * Classe de auxílio para retorno de diversos status
 * dentro do sistema
 * 
 * @author Itarget
 * @version 3.0
 * @package App
 * @access public
 */
class App_Status {

    public static function situacao($s, $tipoRetorno = 'row') {

        $status = array();
        $status[1] = "<span class='label label-success' style='font-size:14px;'>Ativo</span>";
        $status[2] = "<span class='label label-danger'>Inativo</span>";
        $status[3] = "<span class='label label-warning'>Lesionado</span>";
        $status[4] = "<span class='label label-info'>Tecnico</span>";

        if ($tipoRetorno == 'row') {
            return $status[$s];
        } else {
            return $status;
        }
    }
    
    public static function situacaoExcel($s, $tipoRetorno = 'row') {

        $status = array();
        $status[1] = "Ativo";
        $status[2] = "Inativo";
        $status[3] = "Lesionado";
        $status[4] = "Tecnico";

        if ($tipoRetorno == 'row') {
            return $status[$s];
        } else {
            return $status;
        }
    }

}
