<?php

/**
 * Atraves do codigo do status, retorna a string que representa esse status
 *
 * "0"(cadastrado)
 * "1"(emitido, tipo=E)
 * "2"(cancelado, tipo R ou E)
 * "3"(compensado, tipo=E ou R)
 * "4"(recebido, tipo=R)
 * "5"(depositado, tipo=R)
 * "6"(devolvido -  primeira apresentação, tipo=R ou E)
 * "7"(usado em pagamento com cheque de terceiro, tipo=R)
 * "8"(devolvido -  primeira apresentação, tipo=R ou E)';
 */
class Zend_View_Helper_ItargetStatusCheque extends Zend_View_Helper_Abstract {

    public function itargetStatusCheque($status) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        switch ($status) {
            case Cheque::STATUS_CADASTRADO:
                return $traducao->_('Cadastrado');
                break;
            case Cheque::STATUS_EMITIDO:
                return $traducao->_('Emitido');
                break;
            case Cheque::STATUS_CANCELADO:
                return $traducao->_('Cancelado');
                break;
            case Cheque::STATUS_COMPENSADO:
                return $traducao->_('Compensado');
                break;
            case Cheque::STATUS_RECEBIDO:
                return $traducao->_('Recebido');
                break;
            case Cheque::STATUS_DEPOSITADO:
                return $traducao->_('Depositado');
                break;
            case Cheque::STATUS_DEVOLVIDO:
                return $traducao->_('Devolvido');
                break;
            case Cheque::STATUS_USADO_CHEQUE_TERCEIRO:
                return $traducao->_('Usado');
                break;
            case Cheque::STATUS_DEVOLVIDO_PRIMEIRA_APRESENTACAO:
                return $traducao->_('Primeira apresentacao');
                break;
            default:
                return $traducao->_('Indefinido');
                break;
        }
    }

}