<?php

/**
 * Atraves do codigo do tipo, retorna a string que representa esse tipo
 *
 * Emitido(E) 
 * Recebido(R)
 */
class Zend_View_Helper_ItargetTrabalhoAceito extends Zend_View_Helper_Abstract {

    public function itargetTrabalhoAceito($tipo) {

        $traducao = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

        switch ($tipo) {
            case '0':
                return $traducao->_('Pendente de avaliacao');
                break;

            case '1':
                return $traducao->_('Aprovado');
                break;

            case '2':
                return $traducao->_('Aprovado com consideracao');
                break;

            case '3':
                return $traducao->_('Nao aprovado');
                break;

            case '4':
                return $traducao->_('Nao aprovado com justificativa');
                break;

            case '5':
                return $traducao->_('Devolvido');
                break;

            default:
                return $traducao->_('Nao definido');
                break;
        }
    }

}