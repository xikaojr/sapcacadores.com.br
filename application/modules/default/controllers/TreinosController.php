<?php

class TreinosController extends App_Controller_Default {

    protected $_class = null;
    protected $_userLogged = null;

    public function init() {
        parent::init();
        $this->_class = new Treino();
        $this->view->form = $this->_form = new App_Form_Default_Treino();
        $this->_userLogged = $this->getUserLoggedUser();

        if (empty($this->_userLogged) || $this->_userLogged == null) {
            $this->view->helperPriorityMessenger("Voce precisa estar logado para acessar sua area!");
            $this->_redirect('/auth/login');
        }
    }

    public function indexAction() {
        parent::indexAction();
        $this->view->treinos = $atletas = $this->_class->getList(array('order' => 'tr.data ASC'));
    }

    /**
     * Método padrão de create, caso tenha regras especificas, o método precisa
     * ser rescrito no controller!.
     * @throws Exception
     */
    public function createAction() {

        $this->view->iconAction = 'check-square-o';
        $this->view->titleAction = 'Cadastrar Treino';

        if ($this->post) {
            $this->_class->getDefaultAdapter()->beginTransaction();
            $params = $this->get;

            $this->_form->populate($params);

            try {

                if (!$this->_form->isValid($params['treino'])) {
                    throw new Exception('Por favor, verifique as informacoes e envie novamente.');
                }

                $params['treino']['data'] = App_Date::enEn($this->_form->data->getValue());
                $row = $this->_class->save($params['treino']);

                $this->_class->getDefaultAdapter()->commit();
                $this->view->helperPriorityMessenger('Informacoes salvas', self::MSG_SUCESSO);
                $this->_helper->redirector('edit', $this->_controller, $this->_module, array('id' => $row->id));
            } catch (Exception $e) {
                $this->_class->getDefaultAdapter()->rollback();
                $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage(), self::TYPE_INSERT));
            }
        }

        $this->view->form = $this->_form;
        $this->render('form');
    }

    public function createLocalAction() {

        $this->view->iconAction = 'check-square-o';
        $this->view->titleAction = 'Cadastrar Local de Treino';
        $this->_class = new Local();
        $this->_form = new App_Form_Default_Local();
        if ($this->post) {
            $this->_class->getDefaultAdapter()->beginTransaction();
            $params = $this->get;

            $this->_form->populate($params);

            try {

                if (!$this->_form->isValid($params['local'])) {
                    throw new Exception('Por favor, verifique as informacoes e envie novamente.');
                }

                $row = $this->_class->save($params['local']);

                $this->_class->getDefaultAdapter()->commit();
                $this->view->helperPriorityMessenger('Informacoes salvas', self::MSG_SUCESSO);
                $this->_helper->redirector('treino', $this->_controller, $this->_module, array('id' => $row->id));
            } catch (Exception $e) {
                $this->_class->getDefaultAdapter()->rollback();
                $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage(), self::TYPE_INSERT));
            }
        }

        $this->view->form = $this->_form;
        $this->render('form-local');
    }

    public function editAction() {

        $this->view->iconAction = 'edit';
        $this->view->titleAction = 'Editar Treino';
        $params = $this->get;

        try {


            $id = (isset($params['id']) && !empty($params['id'])) ? (int) $params['id'] : null;

            if (null === $id || $id < 1) {
                $this->view->helperPriorityMessenger('Treino não encontrato ou código inválido!', self::MSG_ERRO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            }

            $dados = end($this->_class->getList(array('id' => $id)));
            $this->_form->populate($dados);

            if (!isset($dados['id']) || empty($dados['id'])) {
                $this->view->helperPriorityMessenger('Registro não encontrado!', self::MSG_ERRO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            }
        } catch (Exception $e) {
            $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage()));
        }

        $this->render('form');
    }

    public function ativarAction() {
        $this->noLayout()->noRender();
        $params = $this->get;

        try {

            $this->_class->getDefaultAdapter()->beginTransaction();

            $params['modificado_por'] = $this->getUserLoggedId();
            $date = new DateTime();
            $params['modificado_em'] = $date;

            $this->_class->update(array('situacao' => $params['status']), "id = " . $params['id']);

            $json['status'] = 1;
            $json['msg'] = "Operação realizada com sucesso!";
            $this->_class->getDefaultAdapter()->commit();
        } catch (App_Db_Exception $e) {
            $this->_class->getDefaultAdapter()->rollBack();
            $json['msg'] = $e->getMessage();
            $json['msg_type'] = 'error';
        }

        echo $this->view->json($json);
    }

    public function presencaAction() {

        $this->view->iconAction = 'check-square-o';
        $this->view->titleAction = 'Presença no treino';
        $params = $this->get;

        $presencaModel = new Presenca();
        $form = new App_Form_Default_Presenca();

        if (empty($params['id'])) {
            $this->view->helperPriorityMessenger('Treino não encontrado!', self::MSG_ERRO);
            $this->_helper->redirector('index', $this->_controller, $this->_module);
        }

        $treino = end($this->_class->getList($params));

        if (empty($treino)) {
            $this->view->helperPriorityMessenger('Treino não encontrado!', self::MSG_ERRO);
            $this->_helper->redirector('index', $this->_controller, $this->_module);
        }

        $this->view->treino = $treino;
        
        $presencaTable = new Presenca();
        $presencas = $presencaTable->fetchAll("treino_id = {$params['id']}");
        $pres = array();
        
        if (count($presencas) && is_object($presencas)) {
            foreach ($presencas->toArray() as $p) {
                $pres['atleta_id'][] = $p['atleta_id'];
            }
            $form->populate($pres);
        }
        
        $this->view->form = $form;

        if ($this->post) {

            $presencaModel->getDefaultAdapter()->beginTransaction();
            $params = $this->get;

            $form->populate($params);

            try {

                if (!$form->isValid($params['presenca'])) {
                    throw new Exception('Por favor, verifique as informacoes e envie novamente.');
                }
                
                $presencaModel->delete("treino_id = " . $form->treino_id->getValue());
                
                foreach ($params['presenca']['atleta_id'] as $value) {
                    $presencaModel->save(array(
                          'atleta_id' => $value
                        , 'treino_id' => $form->treino_id->getValue()
                    ));
                }

                $this->_class->update(array('presenca' => 'S'), "id = " . $form->treino_id->getValue());

                $this->_class->getDefaultAdapter()->commit();
                $this->view->helperPriorityMessenger('Informacoes salvas', self::MSG_SUCESSO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            } catch (Exception $e) {
                $this->_class->getDefaultAdapter()->rollback();
                $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage(), self::TYPE_INSERT));
            }
        }
    }

}
