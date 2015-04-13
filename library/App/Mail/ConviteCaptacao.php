<?php

class App_Mail_ConviteCaptacao {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $captacaoTable = new CaptacoesPessoasTable();
        $p = $captacaoTable->findContatoByPessoaAndCentroCusto($dados['pessoa_id'], $dados['centro_custo_id'], 3, $dados['captacao_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::CONVITECAPTACAO;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {


            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {

                // campos magicos
                $valores = array(
                    'link' => 'http://icongresso.' . CLIENTE . '/empresa/' . $centroCusto . '/confirmacao/index/hash/' . $p['hash_link_confirmacao_contrato'],
                    'pessoa_nome' => $p['pessoa_nome']
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

                return $mail->enviar();
            } else {
                throw new Exception(sprintf($translate->_('%s nao tem email'), $p['pessoa_nome']));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido'));
        }
    }

}
