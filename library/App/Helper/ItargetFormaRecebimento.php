<?php

/**
 * Atraves do codigo do recebimento, retorna a string que esse numero representa
 *
 * 1 Dinheiro
 * 2 Cheque de terceiro
 * 4 Cartão de crédito
 * 5 MOVIMENTO NA CC
 * 6 Boleto (só para dizer q pessoa chegou com um boleto pago mas nao deu baixa no
 * banco pq nao teve baixa automática.)
 * 7 Empenho
 * 8 Cheque sem dados
 */
class Zend_View_Helper_ItargetFormaRecebimento extends Zend_View_Helper_Abstract {

    public function itargetFormaRecebimento($id) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        switch ($id) {
            case BaixaContaReceber::BAIXA_DINHEIRO:
                return $traducao->_('Dinheiro');
                break;

            case BaixaContaReceber::BAIXA_CHEQUE_TERCEIRO:
                return $traducao->_('Cheque');
                break;

            case BaixaContaReceber::BAIXA_CARTAO_CREDITO:
                return $traducao->_('Cartao de credito');
                break;

            case BaixaContaReceber::BAIXA_MOVIMENTO_CC:
                return $traducao->_('Movimento em conta');
                break;

            case BaixaContaReceber::BAIXA_BOLETO:
                return $traducao->_('Conta bancaria');
                break;

            default:
                return '';
                break;
        }
    }
}