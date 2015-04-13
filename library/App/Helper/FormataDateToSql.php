<?php

class App_Helper_FormataDateToSql extends App_Helper_Abstract {
    
    /*
     * Recebe da data no formato que vem do banco de dados
     * Ex: 2014-03-26 e retorna 26/03/2014
     * 
     * @return Date Formato Y-m-d
     */

    public function formataDateToSql($date) {
        $date = new Zend_Date($date);
        return $date->toString("Y-m-d");
    }

}
