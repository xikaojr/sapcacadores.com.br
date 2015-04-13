<?php

/**
 * Atraves do codigo do tipo, retorna a string que representa esse tipo
 *
 * Emitido(E) 
 * Recebido(R)
 */
class Zend_View_Helper_ItargetTipoCheque extends Zend_View_Helper_Abstract {

    public function itargetTipoCheque($tipo) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        switch ($tipo) {
            case Cheque::TIPO_EMITIDO:
                return $traducao->_('Emitido');
                break;
            case Cheque::TIPO_RECEBIDO:
                return $traducao->_('Recebido');
                break;
            default:
                return $traducao->_('Indefinido');
                break;
        }
    }

}