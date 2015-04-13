<?php

class App_Mail_InscricaoEvento {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $pessoa = new Pessoa();
        $configuracaoTable = new ConfiguracaoTable();
        $atividades = new Atividade();

        $p = $pessoa->getInfoByCentroCustoId($dados['pessoa_id'], $dados['centro_custo_id']);

        $linhas = $configuracaoTable->findAllByCentroCustoId($dados['centro_custo_id']);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::INSCRICAOEVENTO;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {

            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {
                $view->pessoa = $p;
                $view->logoRodape = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_inscricao_' . $dados['centro_custo_id'] . '.jpg';

                $agenda_atividade_ids = array();
                foreach ($dados['inscricao_ids'] as $id) {
                    $atvRows = $atividades->getDescricaoAtividade($id);
                    $agenda_atividade_ids[] = $atvRows['agenda_atividade_id'];
                }

                $view->agendamentos = $agenda_atividade_ids;

                if (isset($dados['isGrupo']) && $dados['isGrupo']) {
                    $view->isGrupo = $dados['isGrupo'];
                } else {
                    $view->isGrupo = false;
                }

                // define por centro de custo
                $imageJPG = PUBLIC_PATH . '/images/clientes/' . CLIENTE . '/logo_inscricao_' . $dados['centro_custo_id'] . '.jpg';

                if (file_exists($imageJPG)) {
                    $logo = $view->logoRodape;
                } else {
                    $logo = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_inscricao.jpg';
                }

                //tratamento de liberacao de email
                $liberaEnvio = true;
                $descricao = array();
                $valorConfigacao = array();
                $config = false;
                $countATV = 1;

                // monta o array de paramatro
                if (isset($configuracao['165']) && !empty($configuracao['165'])) {
                    // obtendo configuracao
                    $valorConfigacao = $configuracao['165']['valor_referencia'];
                    $valorConfigacao = explode(',', $valorConfigacao);
                    $config = true;
                    $countATV = 0;
                }

                foreach ($dados['inscricao_ids'] as $id) {
                    $atvDesc = $atividades->getDescricaoAtividade($id);
                    if ($config) {
                        if (in_array($atvDesc['id'], $valorConfigacao)) {
                            $descricao[] = $atvDesc['desc_agenda'];
                            $countATV++;
                        }
                    } else {
                        $descricao[] = $atvDesc['desc_agenda'];
                    }
                }
                // bloqueia email caso não tenha nenhuma código setado
                if ($config && $countATV == 0) {
                    $liberaEnvio = false;
                }

                // campos magicos
                $valores = array(
                    'pessoa_nome' => $view->pessoa['pessoa_nome'],
                    'pessoa_email' => $view->pessoa['pessoa_email'],
                    'categoria' => $view->pessoa['categoria_centro_custo_descricao'],
                    'rodape' => '<img src="' . $logo . '" border="0" alt="" />',
                    'grupo' => $view->isGrupo,
                    'empresa_instituicao_nome' => $view->pessoa['empresa_instituicao_nome'],
                    'descricao_agendamento' => implode(',', $descricao),
                        //'inscricao_id' => implode(',', $dados['inscricao_ids'])
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
                if ($liberaEnvio) {
                    return $mail->enviar();
                }
            }
        }
    }

}
