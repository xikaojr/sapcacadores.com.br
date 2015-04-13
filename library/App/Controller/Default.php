<?php

/**
 * Classe Abstrata Geral
 *
 * PHP Version 5.3
 *
 * @copyright (c) 2014, Itarget Tecnologia
 * @link http://itarget.com.br
 * 
 */

/**
 * Description of Inscricao
 *
 * @author Fco Ayrton Junior
 * @package library\App\Controller
 */
class App_Controller_Default extends App_Controller_Action {

    /**
     * nome da sessao do sistema
     */
    const SESSION_STORAGE = 'App_Default';

    protected $_session;
    protected $_auth;
    protected $_form;
    private $configuracao = array();

    public function init() {
        parent::init();
        $this->view->auth = $this->_auth = App_Auth::getStorageSistema($this->getRequest());
        $this->_session = new Zend_Session_Namespace(self::SESSION_STORAGE);
        $this->view->userLoggedId = $this->getUserLoggedId();
    }

    /**
     * Retorna o id do usuário logado
     * @return int|null
     */
    protected function getUserLoggedId() {
        return (isset($this->_auth->getIdentity()->id)) ? $this->_auth->getIdentity()->id : null;
    }

    protected function getUserLoggedName() {
        return (isset($this->_auth->getIdentity()->nome)) ? $this->_auth->getIdentity()->nome : null;
    }

    protected function getUserLoggedUser() {
        return (isset($this->_auth->getIdentity()->id)) ? $this->_auth->getIdentity() : null;
    }

    public function getConfiguracao($codigo) {
        if (isset($this->configuracao[$codigo])) {
            return $this->configuracao[$codigo];
        }

        return null;
    }
    
    

}
