<?php

class App_Mail_ConvitePalestrante {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $palestranteTable = new PalestrantesCentroCustoTable();
        $p = $palestranteTable->findByPessoaAndCentroCusto($dados['pessoa_id'], $dados['centro_custo_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }
        $params['tes.id'] = TiposEmails::CONVITEPALESTRANTE;
        $params['idioma'] = $dados['idioma'];
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {

            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {

                $view->pessoa = $p;

                $valores = array(
                    'link' => 'http://icongresso.' . CLIENTE . '/evento/' . $centroCusto . '/palestrante/cadastro/hash/h/' . $p['hash_link_convite'] . '/lang/' . $dados['idioma'],
                    'pessoa_nome' => $p['pessoa_nome'],
                    'pessoa_email' => $p['pessoa_email'],
                    'funcao_painel' => $p['funcao_painel'],
                    'painel' => $p['painel'],
                    'data_painel' => $p['data_painel'],
                    'hora_ini' => $p['hora_ini'],
                    'hora_fim' => $p['hora_fim'],
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
                throw new Exception($translate->_('Pessoa nao tem email'));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido'));
        }
    }

}
