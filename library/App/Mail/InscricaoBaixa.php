<?php

class App_Mail_InscricaoBaixa {

    public static function enviar($dados) {
        //Iniciando a library de tradução
        $translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');

        $view = Zend_Registry::get('view');

        $pessoaTable = new Pessoa();
        $inscricaoTable = new Inscricao();

        $p = $pessoaTable->getInfoByCentroCustoId($dados['pessoa_id'], $dados['centro_custo_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::INSCRICAOBAIXA;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];
        if (!empty($conteudoEmail)) {

            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {
                $view->pessoa = $p;
                $view->linkRecibo = 'http://icongresso.' . CLIENTE . '/evento/' . $centroCusto;

                if (isset($dados['isGrupo']) && $dados['isGrupo']) {
                    $view->isGrupo = $dados['isGrupo'];
                } else {
                    $view->isGrupo = false;
                }

                //tratamento de liberacao de email
                $liberaEnvio = true;

                // verifica cortesia retornando 

                if (isset($dados['inscricao_id'])) {

                    if ($pessoaTable->geraReciboBoleto($dados['inscricao_id']) == 'S') {
                        $recibo = true;
                    } else {
                        $recibo = false;
                    }

                    $atividades = new Atividade();
                    $atvDesc = $atividades->getDescricaoAtividade($dados['inscricao_id']);

                    // liberacao de email por atividade
                    if (isset($configuracao['154']) && !empty($configuracao['154'])) {
                        // obtendo configuracao
                        $valorConfigacao = $configuracao['154']['valor_referencia'];
                        $valorConfigacao = explode(',', $valorConfigacao);
                        if (!in_array($atvDesc['id'], $valorConfigacao)) {
                            $liberaEnvio = false;
                        }
                    }

                    if (!empty($atvDesc)) {
                        $desc_agenda = $atvDesc['desc_agenda'];
                        $desc_atividade = $atvDesc['desc_atividade'];
                    }
                } else {
                    $recibo = true;
                    $desc_agenda = '';
                    $desc_atividade = '';
                }

                // campos magicos
                $valores = array(
                    'pessoa_nome' => $view->pessoa['pessoa_nome'],
                    'pessoa_email' => $view->pessoa['pessoa_email'],
                    'categoria' => $view->pessoa['categoria_centro_custo_descricao'],
                    'grupo' => $view->isGrupo,
                    'recibo' => $recibo,
                    'link_recibo' => '<a href="' . $view->linkRecibo . '">' . $view->linkRecibo . '</a>',
                    'descricao_atividade' => $desc_atividade,
                    'descricao_agendamento' => $desc_agenda,
                    'matricula' => $view->pessoa['matricula'],
                    'empresa_instituicao_nome' => $view->pessoa['empresa_instituicao_nome'],
                    'inscricao_id' => $dados['inscricao_id']
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($p['pessoa_email'], $p['pessoa_nome'])
                        ->mensagem($conteudoEmail);

                $copia = $rsConteudoEmails['copia'];

                if (!empty($copia)) {
                    $copia = explode(',', $copia);

                    foreach ($copia as $c) {
                        $mail->copiaOculta($c);
                    }
                }

                //verificando se eh inscricao grupo
                if (isset($dados['inscricao_id']) && !empty($dados['inscricao_id'])) {
                    $inscricao = $inscricaoTable->findGrupoInscricao($dados['inscricao_id']);

                    if (count($inscricao)) {
                        foreach ($inscricao as $value) {
                            $enviarGrupo = array(
                                'pessoa_id' => $value['pessoa_id'],
                                'centro_custo_id' => $dados['centro_custo_id'],
                                'isGrupo' => $dados['isGrupo'],
                                'inscricao_id' => $value['id']
                            );
                            self::enviarGrupo($enviarGrupo);
                            unset($enviarGrupo);
                        }
                    }
                }

                if ($liberaEnvio) {
                    return $mail->enviar();
                }
            }
        }
    }

    protected static function enviarGrupo($dados) {
        $translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $pessoaTable = new Pessoa();

        $p = $pessoaTable->getInfoByCentroCustoId($dados['pessoa_id'], $dados['centro_custo_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $idioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt-br' : 'en';
        $arqEmailIns = 'clientes/' . str_replace('.', '-', CLIENTE) . '/default/views/scripts/modelos-documentos/emails/inscricoes/' . $centroCusto . '-baixa-' . $idioma . '.phtml';
        $file = APP_PATH . $arqEmailIns;

        if (is_file($file)) {

            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {
                $view->pessoa = $p;
                $view->linkRecibo = 'http://icongresso.' . CLIENTE . '/evento/' . $centroCusto;

                $logoRodape = "../images/clientes/" . CLIENTE . "/logo_inscricao_" . $centroCusto . ".jpg";

                if (file_exists($logoRodape)) {
                    $view->logoRodape = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_inscricao_' . $centroCusto . '.jpg';
                } else {
                    $logoRodape = "images/clientes/" . CLIENTE . "/logo_inscricao_" . $centroCusto . ".jpg";
                    if (file_exists($logoRodape)) {
                        $view->logoRodape = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_inscricao_' . $centroCusto . '.jpg';
                    } else {
                        $view->logoRodape = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_inscricao.jpg';
                    }
                }

                $logoTopo = "images/clientes/" . CLIENTE . "/topo_inscricao_" . $centroCusto . ".jpg";

                if (file_exists($logoTopo)) {
                    $view->logoTopo = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/topo_inscricao_' . $centroCusto . '.jpg';
                }

                if (isset($dados['isGrupo']) && $dados['isGrupo']) {
                    $view->isGrupo = $dados['isGrupo'];
                } else {
                    $view->isGrupo = false;
                }

                //$conteudoEmail = $view->render($arqEmailIns);
                $conteudoEmail = file_get_contents($file);
                $de = (isset($configuracao['147'])) ? $configuracao['147']['valor_referencia'] : '';

                //tratamento de liberacao de email
                $liberaEnvio = true;

                // verifica cortesia retornando 

                if (isset($dados['inscricao_id'])) {

                    if ($pessoaTable->geraReciboBoleto($dados['inscricao_id']) == 'S') {
                        $recibo = true;
                    } else {
                        $recibo = false;
                    }

                    $atividades = new Atividade();
                    $atvDesc = $atividades->getDescricaoAtividade($dados['inscricao_id']);

                    // liberacao de email por atividade
                    if (isset($configuracao['154']) && !empty($configuracao['154'])) {
                        // obtendo configuracao
                        $valorConfigacao = $configuracao['154']['valor_referencia'];
                        $valorConfigacao = explode(',', $valorConfigacao);
                        if (!in_array($atvDesc['id'], $valorConfigacao)) {
                            $liberaEnvio = false;
                        }
                    }

                    if (!empty($atvDesc)) {
                        $desc_agenda = $atvDesc['desc_agenda'];
                        $desc_atividade = $atvDesc['desc_atividade'];
                    }
                } else {
                    $recibo = true;
                    $desc_agenda = '';
                    $desc_atividade = '';
                }
                // campos magicos
                $valores = array(
                    'pessoa_nome' => $view->pessoa['pessoa_nome'],
                    'categoria' => $view->pessoa['categoria_centro_custo_descricao'],
                    'rodape' => '<img src="' . $view->logoRodape . '" border="0" alt="" />',
                    'topo' => '<img src="' . $view->logoTopo . '" border="0" alt="" />',
                    'grupo' => $view->isGrupo,
                    'recibo' => $recibo,
                    'link_recibo' => '<a href="' . $view->linkRecibo . '">' . $view->linkRecibo . '</a>',
                    'descricao_atividade' => $desc_atividade,
                    'descricao_agendamento' => $desc_agenda,
                    'matricula' => $view->pessoa['matricula']
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_('SAE BRASIL | Confirmação de Inscrição / SAE BRASIL | Registration Confirmation'))
                        ->de($de)
                        ->para($p['pessoa_email'], $p['pessoa_nome'])
                        ->mensagem($conteudoEmail);

                $copia = (isset($configuracao['148'])) ? $configuracao['148']['valor_referencia'] : '';

                if (!empty($copia)) {
                    $copia = explode(',', $copia);

                    foreach ($copia as $c) {
                        $mail->copiaOculta($c);
                    }
                }
                if ($liberaEnvio) {
                    return $mail->enviar();
                }
            }
        }
    }

}
