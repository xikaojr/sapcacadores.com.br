<?php

/**
 * Certificados
 *
 * PHP Version 5.3
 *
 * @copyright (c) 2014, Itarget Tecnologia
 * @link http://itarget.com.br


  /**
 * @todo colocar descricao da class
 */
class App_Controller_Action extends Zend_Controller_Action {

    const MSG_ALERTA = 'alerta';
    const MSG_SUCESSO = 'sucesso';
    const MSG_ERRO = 'erro';
    const TYPE_INSERT = 0;
    const TYPE_DELETE = 1;

    protected $_usuario;
    protected $_appSession;
    protected $get;
    protected $_auth;
    protected $post;
    protected $_class;
    protected $_action;
    protected $_controller;
    protected $_module;
    protected $_belongsto;
    protected $_form;

    /**
     * Iniciando configuracoes do sistema
     */
    public function init() {
        $this->_appSession = new Zend_Session_Namespace('appSession');

        $this->view->auth = $this->_auth = $auth = Zend_Auth::getInstance();

        $this->_usuario = $auth->getStorage()->read();

        $this->_controller = $this->getRequest()->getControllerName();
        $this->view->controller = $this->_controller;

        $this->view->module = $this->_module = $this->getRequest()->getModuleName();
        $this->view->action = $this->_action = $this->getRequest()->getActionName();

        // caso seja uma requisição ajax, vamos desabilitar o layout
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->noLayout();
        }

        $this->get = $this->getRequest()->getParams();
        $this->post = $this->getRequest()->getPost();

        $this->view->usuario = $this->_usuario;
        $this->view->get = $this->get;
        $this->view->post = $this->post;

        parent::init();
    }

    /**
     * Desabilitar o carregamento da view
     * @return \App_Controller_Action
     */
    public function noRender() {
        $this->_helper->viewRenderer->setNoRender();
        return $this;
    }

    /**
     * Desabilita o carragamento do layout
     * @return \App_Controller_Action
     */
    public function noLayout() {
        $this->_helper->layout->disableLayout();
        return $this;
    }

    /**
     * Set layout para ser carregado
     * @param string $layout layout 
     * @return \App_Controller_Action
     */
    public function setLayout($layout) {
        $this->_helper->layout()->setLayout($layout);
        return $this;
    }

    /**
     * Verifica se esta sendo mandado um requisao com metodo ajax
     * @return bool
     */
    public function isAjax() {
        return $this->getRequest()->isXmlHttpRequest();
    }

    /**
     * Verifica se esta sendo mandado um requisao com metodo post
     * @return bool
     */
    public function isPost() {
        return $this->getRequest()->isPost();
    }

    /**
     * Página Inicial
     */
    public function indexAction() {
        $this->view->titleAction = 'Listagem do(a)s ' . ucfirst($this->_controller);
        $this->view->helperGridFiles();
    }

//    LEMBRAR DE EXCLUIR POR QUE SO PEGA UM REGISTRO
    public function salvarJsonAction() {
        $this->noLayout()->noRender();
        $params = $this->getRequest()->getParams();

        try {

            $this->_class->getDefaultAdapter()->beginTransaction();

            $params['criado_por'] = $this->_usuario->id;

            Itarget_Date::converterData($params);

            $dados = $this->_class->save($params);

            $json['dados'] = $dados->id;
            $json['status'] = 1;
            $json['msg'] = "Informações salvas com sucesso!";
            $this->_class->getDefaultAdapter()->commit();
        } catch (App_Db_Exception $e) {
            $this->_class->getDefaultAdapter()->rollBack();
            $json['msg'] = $e->getMessage();
            $json['msg_type'] = 'error';
        }

        echo $this->view->json($json);
    }

    /**
     * Método padrão de create, caso tenha regras especificas, o método precisa
     * ser rescrito no controller!.
     * @throws Exception
     */
    public function createAction() {

        $form = $this->view->form = $this->_form;
        $this->view->iconAction = 'check-square-o';
        $this->view->titleAction = 'Cadastrar ' . ucfirst($this->_controller);

        if ($this->getRequest()->isPost()) {
            $this->_class->getDefaultAdapter()->beginTransaction();
            $params = $this->get;

            if (isset($params[$this->_belongsto])) {
                $params = $params[$this->_belongsto];
            }

            try {
                if (!$form->isValid($params)) {
                    $form->populate($params);
                    throw new Exception($this->_translate->_('Por favor, verifique as informacoes e envie novamente.'));
                }

                $id = $this->_class->save($params);

                if (isset($params['id']) && !empty($params['id'])) {
                    $id = $params['id'];
                }

                $this->_class->getDefaultAdapter()->commit();
                $this->view->helperPriorityMessenger($this->_translate->_('Informacoes salvas'), self::MSG_SUCESSO);
                $this->_helper->redirector('edit', $this->_controller, $this->_module, array('id' => $id));
            } catch (Exception $e) {
                $this->_class->getDefaultAdapter()->rollback();
                $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage(), self::TYPE_INSERT));
            }
        }

        $this->view->form = $this->_form;
        $this->render('form');
    }

    public function editAction() {

        $params = $this->get;
        $this->view->iconAction = 'edit';
        $this->view->titleAction = 'Editar ' . ucfirst($this->_controller);

        try {


            $id = (isset($params['id']) && !empty($params['id'])) ? (int) $params['id'] : null;

            if (null === $id || $id < 1) {
                $this->view->helperPriorityMessenger($this->_translate->_('Identificador inválido!'), self::MSG_ERRO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            }

            $dados = $this->_class->fetchRow("id = {$id}");

            if (count($dados)) {
                $dados = $dados->toArray();
            }

            $this->_form->populate($dados);

            if (!isset($dados['id']) || empty($dados['id'])) {
                $this->view->helperPriorityMessenger($this->_translate->_('Registro não encontrado!'), self::MSG_ERRO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            }
        } catch (Exception $e) {
            $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage()));
        }

        $this->render('form');
    }

//    LEMBRAR DE EXCLUIR POR QUE SO PEGA UM REGISTRO
    public function excluirAction() {
        $this->noLayout()->noRender();

        $this->_class->getAdapter()->beginTransaction();

        try {
            $params = $this->getRequest()->getParams();

            $dados = $this->_class->delete("id = " . $params['id']);

            $retorno = array(
                'status' => 1,
                'msg' => "Excluído com sucesso",
                'msg_type' => 'success',
                'dados' => $dados
            );

            $this->_class->getAdapter()->commit();
        } catch (App_Db_Exception $e) {
            $retorno = array(
                'status' => 2,
                'msg' => $e->getMessage(),
                'msg_type' => 'error'
            );
            $this->_class->getAdapter()->rollBack();
        }

        echo $this->view->json($retorno);
    }

    /**
     * Action padrão para usar o deletar do flexGrid(um ou mais registros);
     */
    public function deleteAction() {
        $this->noLayout()->noRender();

        $linhas = $this->getRequest()->getParam('linhas', array());
        $retorno = array();

        if (count($linhas)) {
            foreach ($linhas as $l) {

                try {
                    $this->_class->delete("id = {$l}");
                    $retorno['msg'] = 'Registro(s) deletado(s) com sucesso!';
                    $retorno['status'] = 1;
                } catch (Exception $e) {
                    $retorno['status'] = 2;
                    $retorno['msg'] = App_Db_Exception::translateMessage(App_Helper_Abstract::mostraMensagemBanco($e->getMessage()));
                }
            }
        }

        echo Zend_Json_Encoder::encode($retorno);
    }

}
