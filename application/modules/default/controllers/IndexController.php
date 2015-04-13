<?php

class IndexController extends App_Controller_Default {

    public function init() {
        parent::init();
        $this->_userLogged = $this->getUserLoggedUser();

        if (empty($this->_userLogged) || $this->_userLogged == null) {
            $this->view->helperPriorityMessenger("Voce precisa estar logado para acessar sua area!");
            $this->_redirect('/auth/login');
        } else {
            $this->view->helperPriorityMessenger("Bem Vindo!");
            $this->_redirect('/atletas');
        }
    }

}
