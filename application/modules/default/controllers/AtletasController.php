<?php

class AtletasController extends App_Controller_Default {

    protected $_class = null;
    protected $_userLogged = null;

    public function init()
    {
        parent::init();
        $this->_class = new Atleta();
        $this->view->form = $this->_form = new App_Form_Default_Atleta_Form();

        if (!is_null($this->getUserLoggedId()) && $this->getUserLoggedId() != 1) {
            $this->view->helperPriorityMessenger("Voce precisa ser o ADMINISTRADOR para acessar a área dos atletas!");
            $this->_redirect('/treinos');
        }
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->atletas = $atletas = $this->_class->getList(array('order' => 'nome'));
    }

    public function listJsonAction()
    {
        $this->noRender()->noLayout();
        $data = $this->_class->getAll('nome');
        echo Zend_Json_Encoder::encode($data);
    }

    /**
     * @type void
     * Faz o upload da foto enviada!
     * Não recebe parametros. 
     * @throws Zend_Form_Exception
     * @throws ModelException
     * @throws Exception
     */
    public function uploadFoto($foto, $row)
    {

        $nomeArquivo = $foto->getFileName();

        if (!empty($nomeArquivo)) {
            // renomeando a foto para o codigo da pessoa e recebendo a foto
            $ext = strtolower(end(explode('.', $nomeArquivo)));
            $novoNome = "{$row->id}.{$ext}";

            try {

                $foto->addFilter('Rename', array('target' => $novoNome, 'overwrite' => true));
                $foto->receive();
                chdir($foto->getDestination());
                rename($nomeArquivo, $novoNome);

                $row->foto = $novoNome;
                $d = $row->toArray();
                $this->_class->save($d);
                chmod($novoNome, 0777);
            } catch (Exception $exc) {
                throw new Exception($exc->getMessage());
            }
        }
    }

    /**
     * Método padrão de create, caso tenha regras especificas, o método precisa
     * ser rescrito no controller!.
     * @throws Exception
     */
    public function createAction()
    {

        $this->view->iconAction = 'check-square-o';
        $this->view->titleAction = 'Cadastrar ' . ucfirst($this->_controller);

        if ($this->post) {
            $this->_class->getDefaultAdapter()->beginTransaction();
            $params = $this->get;

            $this->_form->populate($params);

            try {

                $filter = new Zend_Filter_Digits();

                if (!$this->_form->isValid($params['atleta'])) {
                    throw new Exception('Por favor, verifique as informacoes e envie novamente.');
                }

                if (!$this->_form->rg->getValue()) {
                    $params['atleta']['rg'] = null;
                }

                if ($this->_form->cpf->getValue()) {
                    $params['atleta']['cpf'] = $filter->filter($this->_form->cpf->getValue());
                } else {
                    $params['atleta']['cpf'] = null;
                }


                $params['atleta']['data_nascimento'] = App_Date::enEn($this->_form->data_nascimento->getValue());
                $params['atleta']['entrou_em'] = App_Date::enEn($this->_form->entrou_em->getValue());

                if (!empty($params['atleta']['telefone'])) {
                    $params['atleta']['telefone'] = $filter->filter($this->_form->telefone->getValue());
                }

                if (!empty($params['atleta']['celular'])) {
                    $params['atleta']['celular'] = $filter->filter($this->_form->celular->getValue());
                }

                if (!empty($params['atleta']['celular_contato_emergencia'])) {
                    $params['atleta']['celular_contato_emergencia'] = $filter->filter($this->_form->celular_contato_emergencia->getValue());
                }

                if (!empty($params['atleta']['telefone_contato_emergencia'])) {
                    $params['atleta']['telefone_contato_emergencia'] = $filter->filter($this->_form->telefone_contato_emergencia->getValue());
                }

                $row = $this->_class->save($params['atleta']);

                if (!empty($_FILES)) {
                    $this->uploadFoto($this->_form->foto, $row);
                }

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

    /**
     * Método padrão de create, caso tenha regras especificas, o método precisa
     * ser rescrito no controller!.
     * @throws Exception
     */
    public function createOutsideAction()
    {

        $this->view->iconAction = 'check-square-o';
        $this->setLayout('extra');
        $this->view->titleAction = 'Cadastrar ' . ucfirst($this->_controller);

        if ($this->post) {
            $this->_class->getDefaultAdapter()->beginTransaction();
            $params = $this->get;

            $this->_form->populate($params);

            try {

                $filter = new Zend_Filter_Digits();

                if (!$this->_form->isValid($params['atleta'])) {
                    throw new Exception('Por favor, verifique as informacoes e envie novamente.');
                }

                $params['atleta']['cpf'] = $filter->filter($this->_form->cpf->getValue());
                $params['atleta']['data_nascimento'] = App_Date::enEn($this->_form->data_nascimento->getValue());
                $params['atleta']['entrou_em'] = App_Date::enEn($this->_form->entrou_em->getValue());

                if (!empty($params['atleta']['telefone'])) {
                    $params['atleta']['telefone'] = $filter->filter($this->_form->telefone->getValue());
                }

                if (!empty($params['atleta']['celular'])) {
                    $params['atleta']['celular'] = $filter->filter($this->_form->celular->getValue());
                }

                if (!empty($params['atleta']['celular_contato_emergencia'])) {
                    $params['atleta']['celular_contato_emergencia'] = $filter->filter($this->_form->celular_contato_emergencia->getValue());
                }

                if (!empty($params['atleta']['telefone_contato_emergencia'])) {
                    $params['atleta']['telefone_contato_emergencia'] = $filter->filter($this->_form->telefone_contato_emergencia->getValue());
                }

                $row = $this->_class->save($params['atleta']);

                if (!empty($_FILES)) {
                    $this->uploadFoto($this->_form->foto, $row);
                }

                $this->_class->getDefaultAdapter()->commit();
                $this->view->helperPriorityMessenger('Cadastro realizado com sucesso!', self::MSG_SUCESSO);
                $this->_helper->redirector('create-outside', $this->_controller, $this->_module);
            } catch (Exception $e) {
                $this->_class->getDefaultAdapter()->rollback();
                $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage(), self::TYPE_INSERT));
            }
        }

        $this->view->form = $this->_form;
        $this->render('form-outside');
    }

    public function editAction()
    {

        $this->view->iconAction = 'edit';
        $this->view->titleAction = 'Editar ' . ucfirst($this->_controller);
        $params = $this->get;

        try {


            $id = (isset($params['id']) && !empty($params['id'])) ? (int) $params['id'] : null;

            if (null === $id || $id < 1) {
                $this->view->helperPriorityMessenger('Atelta não encontrato ou código inválido!', self::MSG_ERRO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            }

            $dados = $this->_class->fetchRow("id = {$id}");

            if (count($dados)) {
                $dados = $dados->toArray();
            }

            $dados['data_nascimento'] = App_Date::ptBr($dados['data_nascimento'], false);
            $dados['entrou_em'] = App_Date::ptBr($dados['entrou_em'], false);

            if (strlen($dados['peso']) == 3) {
                $dados['peso'] = (float) $dados['peso'] . '.00';
            }

            $this->_form->populate($dados);

            if (!empty($dados['foto'])) {
                $this->view->foto = $this->view->baseUrl("arquivos/fotos/{$dados['foto']}");
            }

            if (!isset($dados['id']) || empty($dados['id'])) {
                $this->view->helperPriorityMessenger('Registro não encontrado!', self::MSG_ERRO);
                $this->_helper->redirector('index', $this->_controller, $this->_module);
            }
        } catch (Exception $e) {
            $this->view->helperPriorityMessenger(App_Db_Exception::translateMessage($e->getMessage()));
        }

        $this->render('form');
    }

    public function ativarAction()
    {
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

    public function setApelidoAction()
    {
        $this->noLayout()->noRender();
        $params = $this->get;

        try {

            $this->_class->getDefaultAdapter()->beginTransaction();

            $params['modificado_por'] = $this->getUserLoggedId();
            $date = new DateTime();
            $params['modificado_em'] = $date;

            $this->_class->update(array('apelido' => $params['apelido']), "id = " . $params['id']);

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

    public function exportToXlsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $dados = $this->_class->getAll('nome');

        $excel = new Sap_Export_Excel();

        $nomeArquivo = 'Relatorio';
        $caracteres = array(' ', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'W', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AX', 'AY', 'AW', 'AZ', 'BA');

        $excel->getProperties()->setCreator("iTarget Tecnologia");
        $excel->getProperties()->setLastModifiedBy("iTarget Tecnologia");
        $excel->getProperties()->setTitle($nomeArquivo);
        $excel->getProperties()->setSubject($nomeArquivo);
        $excel->getProperties()->setDescription($nomeArquivo);
        $excel->setActiveSheetIndex(0);

        // campos topo
        $i = 0;

        unset($dados[0]['criado_por']);
        unset($dados[0]['criado_em']);
        unset($dados[0]['modificado_por']);
        unset($dados[0]['modificado_em']);

        foreach ($dados[0] as $k => $c) {
            $excel->getActiveSheet()->SetCellValue($caracteres[$i++ + 1] . 1, $k);
        }

        $numeroLinha = 2;

        foreach ($dados as $row) {
            $numeroColuna = 1;

            unset($row['criado_por']);
            unset($row['criado_em']);
            unset($row['modificado_por']);
            unset($row['modificado_em']);

            $row['data_nascimento'] = !empty($row['data_nascimento']) ? App_Date::ptBr($row['data_nascimento'], false) : "";
            $row['entrou_em'] = !empty($row['entrou_em']) ? App_Date::ptBr($row['entrou_em'], false) : "";
            $row['cpf'] = !empty($row['cpf']) ? App_Utilidades::mask($row['cpf'], '999.999.999-99') : "";
            $row['telefone'] = !empty($row['telefone']) ? App_Utilidades::mask($row['telefone'], '(99)9999-9999') : "";
            $row['celular'] = !empty($row['telefone']) ? App_Utilidades::mask($row['telefone'], '(99)9999-9999') : "";
            $row['telefone_contato_emergencia'] = !empty($row['telefone_contato_emergencia']) ? App_Utilidades::mask($row['telefone_contato_emergencia'], '(99)9999-9999') : "";
            $row['situacao'] = !empty($row['situacao']) ? App_Status::situacaoExcel($row['situacao']) : "";

            foreach ($row as $campo => $valor) {
                $excel->getActiveSheet()->SetCellValue($caracteres[$numeroColuna] . $numeroLinha, $valor);
                $numeroColuna++;
            }
            $numeroLinha++;
        }

        $fonteTitulo = array('font' => array('bold' => true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

        $excel->getActiveSheet()->setTitle('Sap Cacadores');
        $excel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($fonteTitulo);

        // Salvando o arquivo temporario
        $objWriter = new PHPExcel_Writer_Excel5($excel);
        $nomeArquivoTemp = '/tmp/' . $nomeArquivo . '_' . date('d-m-Y__') . uniqid(time()) . '.xls';
        $objWriter->save($nomeArquivoTemp);

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header("Content-Type: text/xls charset=UTF-8; encoding=UTF-8");
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '_' . date('d-m-Y') . '.xls";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($nomeArquivoTemp));

        readfile($nomeArquivoTemp);

        exit;
    }

}
