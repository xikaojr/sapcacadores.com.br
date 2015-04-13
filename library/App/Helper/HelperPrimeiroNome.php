<?php
class Zend_View_Helper_HelperPrimeiroNome extends Zend_View_Helper_Abstract
{
    public function helperPrimeiroNome ($nome)
    {
        $exp = explode(" ",$nome);
        return $exp[0];
    }
}