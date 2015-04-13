<?php

/**
 * Atraves do codigo da situacao, retorna a string que representa essa situacao
 */
class Zend_View_Helper_ItargetSituacaoContaPagar extends Zend_View_Helper_Abstract {

    public function itargetSituacaoContaPagar($situacao) {

        $traducao = Zend_Registry::get('translate');

        switch ($situacao) {
            case ContaPagar::SITUACAO_PENDENTE:
                return "<strong style='color: red'>{$traducao->_('Pendente')}</strong>";
                break;
            case ContaPagar::SITUACAO_QUITADO:
                return "<strong style='color: blue'>{$traducao->_('Quitado')}</strong>";
                break;
            default:
                return $traducao->_('Indefinido');
                break;
        }
    }

}