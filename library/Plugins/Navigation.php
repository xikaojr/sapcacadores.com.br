<?php

class Plugins_Navigation extends Zend_Controller_Plugin_Abstract {

    /**
     * Monta o menu de navegacao, de acordo com o arquivo xml
     *
     * @param Zend_View $view
     * @param Zend_Navigation_Page_Uri $menu
     * @param string $sistema - Nome do storage do sistema (ex: Itarget_Controller_Icongresso_Admin::SESSION_STORAGE)
     * @return string
     */
    public static function lerMenu($view, $menu, $storageSistema) {
        
        $acl = App_Acl::getInstance($storageSistema->id);

        $id = (isset($storageSistema->id)) ? (string) $storageSistema->id : null;
        $resource = $menu->getResource();
        
        if (!empty($resource) && !$acl->isAllowed($id, $resource, $menu->getPrivilege())) {
            return;
        }

        /**
         * Pode ter sido adicionado HTML ao inves do link
         * Mesmo nao sendo um link(sendo um HTML), eh necessario que o campo <uri>
         * seja preenchido, caso contrario o script o tratara como um no pai.
         */
        $icon = strlen($menu->get('icon')) > 0 ? '<i class="fa '.$menu->get('icon') . '"></i>' : '';

        if (strlen($menu->get('html')) > 0) {

            $out = "<li><a descricao='{$menu->get('description')}' title='{$menu->getTitle()}' href='{$menu->getHref()}'>{$icon} {$menu->getLabel()}{$menu->get('html')}</a>";
        } else {
            $out = (strlen($menu->getHref()) == 0) ?
                    // Esse no possui no filhos, entao tera a classe dir
                    "<li class='dir'>{$icon} {$menu->getLabel()}<ul>" :
                    // Esse no nao possui no filhos, eh um link
                    "<li class='sub-menu'><a descricao='{$menu->get('description')}' title='{$menu->getTitle()}' href='{$menu->getHref()}'>{$icon} {$menu->getLabel()}</a>";
        }

        // Montando os nos filhos recursivamente
        foreach ($menu as $m) {
            $out .= self::lerMenu($view, $m, $storageSistema);
        }

        // Esse no eh um pai?
        $out .= ( strlen($menu->getHref()) == 0) ? '</ul>' : '';
        // Fim do no
        $out .= '</li>';

        return $out;
    }

}
