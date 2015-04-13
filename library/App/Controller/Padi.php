<?php

/**
 * Padi
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
 * @author Ayrton Junior
 * @package library\App\Controller
 */
class App_Controller_Padi extends App_Controller_Action {

    /**
     * nome da sessao do sistema
     */
    const SESSION_STORAGE = 'App_Padi';

    protected $_session;
    protected $_auth;
    protected $_centroCusto;
    private $_configuracao = array();
    protected $_tipoLogin;//Tipo de login: C-Clínica A-Auditor/Administrador
    
    public function init() {
        parent::init();
        /**
         * @todo adicionar no configurador o centro de custo padi
         */
        $this->view->centroCusto = $this->_centroCusto = $this->get['centro-custo'] = 55;
        $this->view->auth = $this->_auth = App_Auth::getStorageSistema($this->getRequest());

        $params = $this->get;
        
       $this->view->tipoLogin = $this->_tipoLogin  = ($this->getUserLoggedUser()->pessoa_id ? "C" : "A");
       
        //não logado fora do auth
        if (empty($this->_auth->getIdentity()->id) && $params['controller'] != 'auth' && $params['controller'] != 'inscricao') {
            
            $this->_redirect('/padi/auth');
        }
        
        //logado no auth
        if (!empty($this->_auth->getIdentity()->id) 
        && $params['controller'] == 'auth'
        && $params['action'] != 'logout') {
           
            $this->_redirect('/padi/painel');
        }
        
        $this->_session = new Zend_Session_Namespace(self::SESSION_STORAGE);

        if (!$this->_session->__isset('centro_custo_id')) {
            if (isset($this->get['centro-custo'])) {
                $this->_session->__set('centro_custo_id', $this->get['centro-custo']);
                setcookie("centro_custo", $this->get['centro-custo']);
            }
        }

        if ($this->_session->__isset('centro_custo_id')) {
            $this->view->centroCusto = $this->view->centro_custo_id = $this->_centroCusto = $this->_session->__get('centro_custo_id');
            $configuracaoTable = new ConfiguracaoTable();

            $linhas = $configuracaoTable->findAllByCentroCustoId($this->_centroCusto);

            foreach ($linhas as $linha) {
                $this->_configuracao[$linha['codigo']] = $linha;
            }

            $this->view->configuracao = $this->_configuracao;
        }
        
        
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
        if (isset($this->_configuracao[$codigo])) {
            return $this->_configuracao[$codigo];
        }

        return null;
    }

}
