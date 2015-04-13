<?php

class Zend_View_Helper_ItargetStatusVoucher extends Zend_View_Helper_Abstract {

    public function itargetStatusVoucher($tipo) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        switch ($tipo) {
            case Voucher::STATUS_INATIVO:
                return $traducao->_('Inativo');
                break;
            case Voucher::STATUS_ATIVO:
                return $traducao->_('Ativo');
                break;
            case Voucher::STATUS_USADO:
                return $traducao->_('Utilizado');
                break;
            default:
                return $traducao->_('Indefinido');
                break;
        }
    }

}