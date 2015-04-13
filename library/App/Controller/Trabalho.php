<?php

/**
 * Certificado
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
class App_Controller_Trabalho extends App_Controller_Action {

    /**
     * nome da sessao do sistema
     */
    const SESSION_STORAGE = 'App_Trabalho';

    protected $_session;
    protected $_auth;
    protected $_centroCusto;
    private $configuracao = array();

    public function init() {
        parent::init();

        $this->view->auth = $this->_auth = App_Auth::getStorageSistema($this->getRequest());
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
                $this->configuracao[$linha['codigo']] = $linha;
            }

            $this->view->configuracao = $this->configuracao;
        }
    }

    /**
     * Retorna o id do usuário logado
     *
     * @return int|null
     */
    protected function getUserLoggedId() {
        return (isset($this->_auth->getIdentity()->id)) ? $this->_auth->getIdentity()->id : null;
    }

    public function getConfiguracao($codigo) {
        if (isset($this->configuracao[$codigo])) {
            return $this->configuracao[$codigo];
        }

        return null;
    }

}
