<?php

class Zend_View_Helper_ItargetStatusInscricao extends Zend_View_Helper_Abstract {

    public function itargetStatusInscricao($id, $color = false) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        switch ($id) {
            case Inscricao::STATUS_PENDENTE:
                return ($color) ? "<span style='color: tomato'>{$traducao->_('Pendente')}</span>" : $traducao->_('Pendente');
                break;

            case Inscricao::STATUS_INSCRITO:
                return ($color) ? "<span style='color: green'>{$traducao->_('Inscrito')}</span>" : $traducao->_('Inscrito');
                break;

            case Inscricao::STATUS_CANCELADO:
                return ($color) ? "<span style='color: tomato'>{$traducao->_('Cancelado')}</span>" : $traducao->_('Cancelado');
                break;

            case Inscricao::STATUS_TRANSFEIRO:
                return $traducao->_('Transferido');
                break;

            default:
                return '';
                break;
        }
    }

}
