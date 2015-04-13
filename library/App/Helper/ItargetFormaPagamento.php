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
class Zend_View_Helper_ItargetFormaPagamento extends Zend_View_Helper_Abstract {

    public function itargetFormaPagamento($id) {

        $traducao = Zend_Registry::get('translate');

        switch ($id) {
            case BaixaContaPagar::BAIXA_DINHEIRO:
                return $traducao->_('Dinheiro');
                break;

            case BaixaContaPagar::BAIXA_CHEQUE_EMPRESA:
                return $traducao->_('Cheque da Empresa');
                break;

            case BaixaContaPagar::BAIXA_CHEQUE_TERCEIRO:
                return $traducao->_('Cheque de Terceiro');
                break;

            case BaixaContaPagar::BAIXA_CARTAO_CREDITO:
                return $traducao->_('Cartao de credito');
                break;

            case BaixaContaPagar::BAIXA_MOVIMENTO_CC:
                return $traducao->_('Movimento em conta');
                break;

            case BaixaContaPagar::BAIXA_BOLETO:
                return $traducao->_('Conta bancaria');
                break;

            default :
                return $traducao->_('Indefinido');
                break;
        }
    }

}