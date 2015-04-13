<?php

/**
 * Sistemas Desenvolvimento
 * 
 * PHP Versao 5.3
 * 
 * @copyright (c) 2014, Itarget tecnologia
 * @link http://www.itarget.com.br
 */

/**
 * Class para formatar a exibição do menu
 */
class Zend_View_Helper_OrganizarMenuList extends Zend_View_Helper_Abstract {

    /**
     * Variável Menu (Model)
     * @var Menu 
     */
    private $_menuTable;

    /**
     * Variável com o array de menus
     * @var array 
     */
    private $_itens;

    /**
     * Organiza o array de menu por hieraquia
     * 
     * @return array
     */
    public function organizarMenuList() {

        $this->_menuTable = new Menu();


        /**
         * Busca todos os menus do primeiro nível
         */
        $itens = $this->_menuTable->fetchAll("menu_id_parent=0 and sistema_id = " . CODIGO_SISTEMA, "ordem")->toArray();

        /**
         * Montando o menu
         */
        if (is_array($itens) && !empty($itens)) {
            foreach ($itens as $key => $link) {
                $itens[$key]['pages'] = $this->_getChildNodes($link['id']);
            }
        }

        return $this->_montarMenu($itens);
    }

    /**
     * Prepara os nós filhos do menu
     * 
     * @param int $menu_id
     * @return array $childNodes
     */
    protected function _getChildNodes($menu_id) {
        /**
         * Verificando a existência de páginas filhas
         */
        $childNodes = $this->_menuTable->fetchAll("menu_id_parent={$menu_id} and sistema_id = " . CODIGO_SISTEMA, "ordem")->toArray();

        if (is_array($childNodes) && !empty($childNodes)) {
            foreach ($childNodes as $key => $link) {
                $childNodes[$key]['pages'] = self::_getChildNodes($link['id']);
            }
        }

        return $childNodes;
    }

    /**
     * Monta do HTML do menu
     * @param array $itens
     */
    protected function _montarMenu(array $itens) {
        $html = array();
        $translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        if (is_array($itens) && !empty($itens)) {
            foreach ($itens as $link) {
                $html[] = '<li class="dd-item" data-id="' . $link['id'] . '" id="list_' . $link['id'] . '">';
                $html[] = '<div class="dd-handle"><span class="disclose"><span></span></span><i class="fa ' . $link['icon'] . '" style="color: #000;"></i> ' . $link['label'] . '<span class="excluir ui-icon ui-icon-trash" title="' . $translate->_('Excluir') . '"></span><span class="editar ui-icon ui-icon-pencil" title="' . $translate->_('Editar') . '"></span></div>';

                /**
                 * Montagem das páginas filhas caso exista
                 */
                if ($link['pages']) {
                    $html[] = '<ol class="">';
                    $html[] = self::_montarMenu($link['pages']);
                    $html[] = '</ol>';
                }

                $html[] = '</li>';
            }
        }

        return implode("\n", $html);
    }

}
