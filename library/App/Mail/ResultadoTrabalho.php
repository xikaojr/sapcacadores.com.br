<?php

class App_Mail_ResultadoTrabalho {

    public static function enviar($dados) {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $trabalhosTable = new Trabalhos();
        $p = $trabalhosTable->getByIdOrStatus($dados['trabalho_id'], $dados['trabalho_fase']);

        $configuracaoTable = new Configuracao();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';
        $params['tes.id'] = TiposEmails::RESULTADOTRABALHO;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($p['email']) && !empty($p['email'])) {

                $conteudo = file_get_contents(MODULES_PATH . 'icase/views/scripts/' . $arqEmail);
                $campos = array(
                    'pessoa_nome' => $p['pessoa_nome'],
                    'email' => $p['email'],
                    'trabalho_numero_sae' => $p['trabalho_numero_sae'],
                    'titulo_lng' => $p['titulo_lng'],
                    'nota_final' => $p['nota_final'],
                    'observacao_final' => $p['observacao_final'],
                    'link_autor' => "http://icongresso." . CLIENTE . "/evento/{$centroCusto}/auth",
                    'link_esqueci_senha' => "http://icongresso." . CLIENTE . "/evento/{$centroCusto}/auth/esqueci-minha-senha",
                    'status' => App_Status::trabalhos($p['status']),
                    'logoRodape' => 'http://icase.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_avaliador_' . $centroCusto . '.jpg'
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($p['email'], $p['pessoa_nome'])
                        ->mensagem($conteudoEmail);

                $copia = $rsConteudoEmails['copia'];
                if (!empty($copia)) {
                    $copia = explode(',', $copia);

                    foreach ($copia as $c) {
                        $mail->copiaOculta($c);
                    }
                }

                return $mail->enviar();
            } else {
                throw new Exception($translate->_('Pessoa nao tem email'));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido'));
        }
    }

}
