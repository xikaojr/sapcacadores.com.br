<?php

class Zend_View_Helper_ItargetStatusTrabalho extends Zend_View_Helper_Abstract {

    public function itargetStatusTrabalho($id, $color = false) {

        $traducao = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        switch ($id) {
            case Trabalhos::STATUS_PENDENTE:
                return ($color) ? '<span class="label label-warning">' . $traducao->_('Pendente') . '</span>' : $traducao->_('Pendente');
                break;

            case Trabalhos::STATUS_SELECIONADO:
                return ($color) ? '<span class="label label-success">' . $traducao->_('Selecionado') . '</span>' : $traducao->_('Selecionado');
                break;

            case Trabalhos::STATUS_SELECIONADO_CONSIDERACAO:
                return ($color) ? '<span class="label label-success">' . $traducao->_('Selecionado com consideracao') . '</span>' : $traducao->_('Selecionado com consideracao');
                break;

            case Trabalhos::STATUS_NAO_SELECIONADO:
                return ($color) ? '<span class="label label-info">' . $traducao->_('Nao selecionado') . '</span>' : $traducao->_('Nao selecionado');
                break;

            case Trabalhos::STATUS_CANCELADO:
                return ($color) ? '<span class="label label-danger">' . $traducao->_('Cancelado') . '</span>' : $traducao->_('Cancelado');
                break;

            default:
                return '';
                break;
        }
    }

}
