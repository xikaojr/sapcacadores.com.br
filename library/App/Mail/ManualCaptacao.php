<?php

class App_Mail_ManualCaptacao {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $captacaoTable = new CaptacoesPessoasTable();
        $p = $captacaoTable->findContatoByPessoaAndCentroCusto($dados['pessoa_id'], $dados['centro_custo_id'], 3, $dados['captacao_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        $captacoesAnexosTable = new CaptacoesAnexosTable();
        $where['captacao_id'] = $dados['captacao_id'];
        $where['tipo_anexo'] = CaptacoesAnexos::MANUAL;
        $captacoesAnexos = $captacoesAnexosTable->findCaptacoesAnexo($where);
        $captacoesAnexos = end($captacoesAnexos);


        $captacaoTable = new CaptacaoTable();
        $date = new Zend_Date;

        if (!$captacoesAnexos) {
            throw new Exception($translate->_('Manual nao definido'));
        }

        $arquivo = 'arquivos/clientes/' . CLIENTE . DS . 'manuais' . DS . $captacoesAnexos['nome_armazenado'];

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }
        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::MANUALCAPTCAO;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {

                $valores = array(
                    'pessoa_nome' => $p['pessoa_nome'],
                    'link' => 'http://icongresso.' . CLIENTE . '/' . $arquivo
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

                $captacaoTable->save(array('id' => $dados['captacao_id'], 'data_envio_manual' => App_Date::enEn($date)));

                return $mail->enviar();
            } else {
                throw new Exception(sprintf($translate->_('%s nao tem email'), $p['pessoa_nome']));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido'));
        }
    }

}
