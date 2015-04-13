<?php

class App_Mail_CobrancaTrabalho {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $trabalhosAvaliadoresTable = new TrabalhosAvaliadores();

        if (isset($dados['trabalho_id']) && !empty($dados['trabalho_id']))
            $pessoas = $trabalhosAvaliadoresTable->getAvaliadoresByTrabalhoId($dados['trabalho_id'], $dados['trabalho_fase']);

        if (isset($dados['pessoa_id']) && !empty($dados['pessoa_id']))
            $pessoas = $trabalhosAvaliadoresTable->getAvaliacoes($dados['pessoa_id'], 0, $dados['fase']);

        $configuracaoTable = new Configuracao();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($pessoas['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::COBRANCAINSCRICAO;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            foreach ($pessoas as $p) {
                if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {

                    if (!isset($dados['trabalho_id'])) {
                        $avaliadorId = $p['avaliador_centro_custo_id'];
                        $trabalhosTable = new Trabalhos();
                        $trabalhos = $trabalhosTable->getTrabalhosByAvaliadorId($avaliadorId);

                        $listaTrabalhos = "<b>Trabalhos as serem avaliados</b>";
                        $listaTrabalhos .= "<ul>";
                        foreach ($trabalhos as $t) {
                            $listaTrabalhos .= "<li>{$t['titulo']}</li>";
                        }
                        $listaTrabalhos .= "</ul>";
                    } else {
                        $listaTrabalhos = "
                            <b>Trabalhos as serem avaliados</b>
                            <ul>
                                <li>{$p['titulo']}</li>
                            </ul>
                        ";
                    }

                    $campos = array(
                        'pessoa_nome' => $p['pessoa_nome'],
                        'lista_trabalhos' => $listaTrabalhos
                    );

                    $conteudoEmail = App_Filtro::camposMagicos($campos, $conteudoEmail);

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
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido'));
        }
    }

}
