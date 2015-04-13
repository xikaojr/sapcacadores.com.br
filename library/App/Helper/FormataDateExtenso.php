<?php

class App_Helper_FormataDateExtenso extends App_Helper_Abstract {

    public function formataDateExtenso($data, $lang = 'pt-BR') {
        $dt = new Zend_Date($data, Zend_Date::ISO_8601, $lang);

        if ($lang == 'pt-BR')
            $data = $dt->toString('d') . ' de ' . $dt->toString('F') . ' de ' . $dt->toString('Y');
        else
            $data = $dt->toString('M') . ', ' . $dt->toString('EEEE, d') . ' of ' . $dt->toString('Y');

        return $data;
    }

}
