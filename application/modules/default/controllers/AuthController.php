<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

class AuthController extends App_Controller_Default {

    public function init() {
        parent::init();
        $this->setLayout('login');
        $this->view->form = $this->_form = new App_Form_Default_Login();
    }

    public function indexAction() {
        $this->_auth->clearIdentity();
        $this->view->form = $this->_form;
    }

    public function loginAction() {
        $params = $this->get;
        $usuariosTable = new Usuarios();
        if ($this->isPost()) {
            try {

                $this->_form->populate($params);

                if ($this->_auth->hasIdentity()) {
                    $this->_auth->clearIdentity();
                }

                if ($usuariosTable->autentica($this->_form->login->getValue(), $this->_form->senha->getValue())) {
                    $this->_redirect('/default/atletas');
                }
            } catch (Exception $e) {
                $this->view->helperPriorityMessenger($e->getMessage(), 'erro');
                $this->_redirect('/default/auth/login');
            }
        }

        $this->render('index');
    }

    public function logoutAction() {

        if ($this->_auth->hasIdentity()) {
            $identity = $this->_auth->getIdentity();
        }

        $this->_auth->clearIdentity();
        $session = new Zend_Session_Namespace(self::SESSION_STORAGE);
        $session->unsetAll();
        $this->_redirect("auth/login");
    }

    public function redefinirSenhaAction() {
        $params = $this->getRequest()->getParams();

        if (isset($params['hash']) && !empty($params['hash'])) {

            $pessoaTable = new Pessoa();
            $result = $pessoaTable->findByHash($params['hash']);

            if (!isset($result['id'])) {
                $this->view->helperPriorityMessenger($this->_translate->_('Voce nao solicitou uma nova senha. Para solicitar digite seu email.'), 'erro');
                $this->_redirect("/inscricao/auth/esqueci-minha-senha/centro-custo/{$this->view->centroCusto}");
            }

            if ($this->_request->isPost()) {
                $senha = $this->getRequest()->getPost('senha');
                $cofirmaSenha = $this->getRequest()->getPost('confirmar_senha');

                if ($senha == "") {
                    $this->view->helperPriorityMessenger($this->_translate->_('Informe sua nova senha!'), 'erro');
                } else if ($cofirmaSenha == "") {
                    $this->view->helperPriorityMessenger($this->_translate->_('Confirme sua nova senha!'), 'erro');
                } else if ($senha != $cofirmaSenha) {
                    $this->view->helperPriorityMessenger($this->_translate->_('Confirme a senha correta!'), 'erro');
                } else {
                    $dados = array();
                    $dados['id'] = $result['id'];
                    $dados['senha'] = strtolower(sha1($senha));
                    $dados['hash_link_senha'] = "-";

                    $pessoaTable->save($dados);

                    $this->view->helperPriorityMessenger($this->_translate->_('Sua senha foi alterada. Voce ja pode acessar com a nova senha.'), 'sucesso');
                    $this->_redirect("/inscricao/auth/login/centro-custo/{$this->view->centroCusto}");
                }
            }
        } else {
            $this->view->helperPriorityMessenger($this->_translate->_('Voce nao solicitou uma nova senha. Para solicitar digite seu email.'), 'erro');
            $this->_redirect("/inscricao/auth/esqueci-minha-senha/centro-custo/{$this->view->centroCusto}");
        }
    }

    public function rememberPasswordAction() {
        $this->noRender()->noLayout();

        if ($this->_request->isPost()) {

            $retorno = array();

            try {

// Tratamento de seguranca
                $filter = new Zend_Filter_StripTags();

                $emailPost = $this->getRequest()->getPost('email', false);
                $emailPost = $filter->filter($emailPost);
                $emailPost = str_replace(' ', '#', $emailPost);
                $emailPost = str_replace(' ', '#', $emailPost);
                $this->view->email = $emailPost;

                $emailFrom = $this->getConfiguracao(138);

                $this->view->emailContato = $emailFrom['valor_referencia'];

                $result = null;
                $pessoaTable = new Pessoa();

                if (empty($result) && !empty($emailPost)) {
                    $result = $pessoaTable->findByEmail($emailPost);
                }

                if (empty($result)) {

                    if (!empty($souSocio) && !empty($matricula)) {
                        throw new Exception($this->_translate->_('A matricula informada nao foi localizada, verifique se sua matricula foi digitada corretamente ou entre em contato com a entidade atraves do e-mail <strong>' . $emailFrom['valor_referencia'] . '</strong>'));
                    } else {
                        throw new Exception($this->_translate->_('A email informada nao foi localizada, verifique se seu email foi digitada corretamente ou entre em contato com a entidade atraves do e-mail <strong>' . $emailFrom['valor_referencia'] . '</strong>'));
                    }
                } else {
                    $this->view->dados = $result;


                    $hash = sha1(md5($emailPost . date('YmdHis')));

                    $pessoaTable->update(array(
                        'hash_link_senha' => $hash
                            ), "id = {$result['id']}");

                    $formPessoa = new App_Form_Default_Pessoa_Fisica();
                    $formPessoa->populate($result);

                    $this->view->hash = $hash;
                    $this->view->translate = $this->_translate;
                    $this->view->form = $formPessoa;
                    $this->view->paramsEmail = array();
                    $this->view->paramsEmail['logo_rodape'] = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logoP_' . $this->view->centroCusto . '.jpg';

                    $basePath = MODULES_PATH . 'inscricao/views/scripts/auth/email/' . str_replace('.', '-', CLIENTE);
                    $arqEmail = "{$basePath}/{$this->view->centroCusto}/email.phtml";

                    if (is_file($arqEmail)) {
                        $emailARenderizar = 'auth/email/' . str_replace('.', '-', CLIENTE) . '/' . $this->view->centroCusto . '/email.phtml';
                    } else {
                        $emailARenderizar = 'auth/email.phtml';
                    }

                    $mail = new App_Mail();

                    $mail->assunto($this->_translate->_('Solicitacao de nova senha'))
                            ->de($emailFrom['valor_referencia'])
                            ->para(strtolower($emailPost))
                            ->copiaOculta('wlisses@itarget.com.br')
                            ->mensagem($this->view->render($emailARenderizar));

                    $mail->enviar();

                    $retorno['status'] = true;
                    $retorno['email'] = strtolower($emailPost);
                    $retorno['msg'] = $this->_translate->_('Dentro de instantes voce recebera um e-mail com o link para redefinir a nova senha!');
                }
            } catch (Zend_Mail_Exception $e) {
                $retorno['status'] = false;
                $retorno['msg'] = $this->_translate->_('E-mail não localizado em nossa base de dados. <br/> Realize seu cadastro e tente novamente ou entre em contato com a associação!');
            } catch (Exception $e) {
                $retorno['status'] = false;
                $retorno['msg'] = $this->_translate->_('E-mail não localizado em nossa base de dados. <br/> Realize seu cadastro e tente novamente ou entre em contato com a associação!');
            }
        }

        echo Zend_Json::encode($retorno);
    }

    public function esqueciMinhaSenhaAction() {
        $this->noLayout();
        if ($this->_request->isPost()) {

// Tratamento de seguranca
            $filter = new Zend_Filter_StripTags();

            $emailPost = $this->getRequest()->getPost('email', false);
            $emailPost = $filter->filter($emailPost);
            $emailPost = str_replace(' ', '#', $emailPost);
            $emailPost = str_replace(' ', '#', $emailPost);
            $this->view->email = $emailPost;

            $souSocio = $this->getRequest()->getPost('sou_socio', '');
            $matricula = $this->getRequest()->getPost('matricula', '');
            $matricula = trim($filter->filter($matricula));
            $emailFrom = $this->configuracao['138'];
            $this->view->emailContato = $emailFrom['valor_referencia'];

            if (!$emailPost && (empty($souSocio) || empty($matricula) )) {

                if (!empty($souSocio) && empty($matricula)) {
                    $this->view->matricula = $matricula;
                    $this->view->helperPriorityMessenger($this->_translate->_('Informe a sua matricula.'), 'erro');
                } else {
                    $this->view->helperPriorityMessenger($this->_translate->_('Informe seu e-mail no campo abaixo!'), 'erro');
                }
            } else {
                $result = null;
                $pessoaTable = new Pessoa();

                if (!empty($souSocio) && !empty($matricula)) {
                    $this->view->matricula = $matricula;
                    $result = $pessoaTable->findByMatricula($matricula);
                }

                if (empty($result) && !empty($emailPost)) {
                    $result = $pessoaTable->findByEmail($emailPost);
                }

                if (empty($result)) {

                    if (!empty($souSocio) && !empty($matricula)) {
                        $this->view->helperPriorityMessenger(sprintf($this->_translate->_('A matricula informada nao foi localizada, verifique se sua matricula foi digitada corretamente ou entre em contato com a entidade atraves do e-mail %s'), "<strong>{$emailFrom['valor_referencia']}</strong>"), 'erro');
                    } else {
                        $this->view->helperPriorityMessenger(
                                sprintf($this->_translate->_('E-mail %s , nao localizado em nossa base de dados. Realize seu cadastro e tente novamente ou entre em contato com a associação'), "<strong>{$emailFrom['valor_referencia']}</strong>"), 'erro');
                    }
                    $this->_redirect("/inscricao/auth/index/centro-custo/{$this->view->centroCusto}");
                } else {
                    $this->view->dados = $result;

                    try {

                        $hash = sha1(md5($emailPost . date('YmdHis')));

                        $pessoaTable->update(array(
                            'hash_link_senha' => $hash
                                ), "id = {$result['id']}");

                        $formPessoa = new App_Form_Default_Pessoa_Fisica();
                        $formPessoa->populate($result);

                        $this->view->hash = $hash;
                        $this->view->translate = $this->_translate;
                        $this->view->form = $formPessoa;
                        $this->view->paramsEmail = array();
                        $this->view->paramsEmail['logo_rodape'] = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logoP_' . $this->view->centroCusto . '.jpg';

                        $basePath = MODULES_PATH . 'inscricao/views/scripts/auth/email/' . str_replace('.', '-', CLIENTE);
                        $arqEmail = "{$basePath}/{$this->view->centroCusto}/email.phtml";

                        if (is_file($arqEmail)) {
                            $emailARenderizar = 'auth/email/' . str_replace('.', '-', CLIENTE) . '/' . $this->view->centroCusto . '/email.phtml';
                        } else {
                            $emailARenderizar = 'auth/email.phtml';
                        }

                        $mail = new App_Mail();

                        $mail->assunto($this->_translate->_('Solicitacao de nova senha'))
                                ->de($emailFrom['valor_referencia'])
                                ->para(strtolower($emailPost))
//                                ->copiaOculta('wlisses@itarget.com.br')
                                ->mensagem($this->view->render($emailARenderizar));

                        $mail->enviar();

                        $this->view->helperPriorityMessenger($this->_translate->_('Dentro de instantes voce recebera um e-mail com o link para redefinir a nova senha!'), 'sucesso');
                        $this->view->dados = $result;
                        $this->_redirect("/inscricao/auth/index/centro-custo/{$this->view->centroCusto}");
                    } catch (Zend_Mail_Exception $e) {
                        echo $e->getMessage();
                        $this->view->helperPriorityMessenger($this->_translate->_('E-mail nao localizado em nossa base de dados. Realize seu cadastro e tente novamente ou entre em contato com a associação!'), 'erro');
                        $this->_redirect("/inscricao/auth/index/centro-custo/{$this->view->centroCusto}");
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        $this->view->helperPriorityMessenger($this->_translate->_('E-mail nao localizado em nossa base de dados. Realize seu cadastro e tente novamente ou entre em contato com a associação!'), 'erro');
                        $this->_redirect("/inscricao/auth/index/centro-custo/{$this->view->centroCusto}");
                    }
                }
            }
        } else {
            $this->render('esqueci-minha-senha');
        }
    }

    /**
     * Pagina para mudar a senha do usuario que solicitou
     * @return void
     */
    public function mudarSenhaAction() {
        $pessoaTable = new Pessoa();

// Se o campo mudar a senha estiver N é porque não solicitou uma nova senha
        if ($this->_auth->getIdentity()->mudar_senha == 'N') {
            $this->view->helperPriorityMessenger($this->_translate->_('Voce nao solicitou um nova senha.'), 'erro');
            $this->_redirect("/inscricao/auth/index/centro-custo/{$this->view->centroCusto}");
        }

// Se tiver post
        if ($this->_request->isPost()) {
// Se a senha for diferente da padrão, muda a senha
            if ($this->getRequest()->getPost('senha') != $this->view->helperParametroConfig(5)) {
                try {
                    $dados = array();
                    $dados['id'] = $this->_auth->getIdentity()->pessoa_id;
                    $dados['mudar_senha'] = 'N';
                    $dados['senha'] = strtolower(sha1($this->getRequest()->getPost('senha')));
                    $this->_auth->getIdentity()->mudar_senha = 'N';

                    $pessoaTable->save($dados);

                    $this->view->helperPriorityMessenger($this->_translate->_('Sua senha foi alterada com sucesso.'), 'sucesso');
                    $this->_redirect("/inscricao/auth/index/centro-custo/{$this->view->centroCusto}");
                } catch (Zend_Mail_Exception $e) {
                    $this->view->helperPriorityMessenger($this->_translate->_('E-mail nao localizado em nossa base de dados. Realize seu cadastro e tente novamente ou entre em contato com a associação!'), 'sucesso');
                }
            } else {
                $this->view->helperPriorityMessenger($this->_translate->_('Sua nova senha nao pode ser a mesma.'), 'erro');
                $this->_redirect("/evento/{$this->view->centroCusto}/auth/mudar-senha");
            }
        }
    }

}
