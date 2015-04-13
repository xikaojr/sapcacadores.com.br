<?php

class App_Helper_FormataDate extends App_Helper_Abstract {
    /*
     * Recebe da data no formato que vem do banco de dados
     * Ex: 2014-03-26 e retorna 26/03/2014
     * 
     * @return Date Formato d/m/Y
     */

    public function formataDate($date) {
        $date = new Zend_Date($date, 'Y-m-d');
        return $date->toString("d/m/Y");
    }
    
    public function formataDateEng($date) {
        $date = new Zend_Date($date, 'd/m/Y');
        return $date->toString("Y-m-d");
    }

    /*
     * Recebe da data no formato que vem do banco de dados
     * Ex: 2014-03-26 e retorna 26/03/2014
     * 
     * @return Date Formato d/m/Y
     */

    public static function formataDateStatic($date) {

        $date = new Zend_Date($date, 'Y-m-d');
        return $date->toString("d/m/Y");
    }


}
