<?php

/**
 * Informa os detalhes sobre como foi feito o pagamento
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
class Zend_View_Helper_ItargetDetalhesPagamento extends Zend_View_Helper_Abstract {

    /**
     * Informa os detalhes sobre o pagamento
     * @param int $id Id da baixa conta receber
     * @return string
     */
    public function itargetDetalhesPagamento($baixaContaReceberId) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
        $baixaContaReceberId = (int) $baixaContaReceberId;
        $baixaContaReceberTable = new BaixaContaReceber();

        $baixaContaReceber = $baixaContaReceberTable->find($baixaContaReceberId);
        $baixaContaReceber = end($baixaContaReceber->toArray());

        $caixaTable = new Caixa();
        $caixa = end($caixaTable->find($baixaContaReceber["caixa_id"])->toArray());

        $contaReceberTable = new ContaReceber();
        $contaReceber = end($contaReceberTable->find($baixaContaReceber["conta_receber_id"])->toArray());

        $controleBoletoCartaoTable = new ControleBoletoCartao();
        $controleBoletoCartao = end($controleBoletoCartaoTable->find($contaReceber["controle_boleto_cartao_id"])->toArray());
        
        $contaBancariaTable = new ContaBancaria();
        $contaBancaria      = end($contaBancariaTable->find($baixaContaReceber["conta_bancaria_id"])->toArray());
//        Zend_Debug::dump($contaBancaria);
//        die;

        if (!$baixaContaReceber["id"]) {
            return '';
        }

        switch ($baixaContaReceber["forma_recebimento"]) {
            case BaixaContaReceber::BAIXA_DINHEIRO:
                return "<strong>{$traducao->_('Caixa:')}</strong> {$caixa['descricao']}";
                break;

            case BaixaContaReceber::BAIXA_CHEQUE_TERCEIRO:
                return $traducao->_('Cheque');
                break;

            case BaixaContaReceber::BAIXA_CARTAO_CREDITO:
                return $traducao->_('Cartao de credito');
                break;

            case BaixaContaReceber::BAIXA_MOVIMENTO_CC:
                //Devido à pressa, foi o jeito fazer assim
//                $controleBoletoCartao = $baixaContaReceber->getContaReceber()->getControleBoletoCartao();

                if ($controleBoletoCartao && strlen($controleBoletoCartao["nome_arq_retorno"]) > 1) {
                    $nomeArqRetorno = $controleBoletoCartao["nome_arq_retorno"];
                } else {
                    $nomeArqRetorno = $traducao->_('N/D');
                }

                return ''
                        . "<strong>{$traducao->_('Conta:')}</strong> {$contaBancaria["conta_num"]}"
                        . "<br /><strong>{$traducao->_('Arquivo retorno:')}</strong> {$nomeArqRetorno}";
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
