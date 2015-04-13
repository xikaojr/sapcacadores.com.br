<?php

class App_Sistema {

    public static function getName() {
        switch (MODULE) {
            case 'icongresso': $nome = 'iCongresso';
                break;
            case 'icase': $nome = 'iCase';
                break;
            case 'pdg': $nome = 'Pdg';
                break;
            default: $nome = '';
        }

        return $nome;
    }

    public static function getSubModuleName() {
        $req = Zend_Controller_Front::getInstance()->getRequest();
        $subModulo = explode('_', $req->getControllerName());
        $subModulo[0] = ucfirst(strtolower($subModulo[0]));
        return (count($subModulo) > 1) ? $subModulo[0] : 'Admin';
    }

}
