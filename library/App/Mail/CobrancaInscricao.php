<?php

class App_Mail_CobrancaInscricao {

    public static function enviar($dados) {
        $translate = Zend_Registry::get('translate');
        $view = Zend_Registry::get('view');

        $contaReceberTable = new ContaReceberTable();
        $contaReceber = $contaReceberTable->findById($dados['conta_receber_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $contaReceber['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($contaReceber['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::COBRANCAINSCRICAO;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($contaReceber['email']) && !empty($contaReceber['email'])) {

                $valores = array(
                    'pessoa_nome' => $contaReceber['nome'],
                    'data_vencimento' => App_Formate::data($contaReceber['data_vencimento']),
                    'valor' => App_Moeda::formatar($contaReceber['valor']),
                    'boleto' => '<a href="http://icase.' . CLIENTE . '/default/boleto/imprimir/numero/' . $contaReceber['num_nosso_numero'] . '/layout/' . $contaReceber['controle_layout_id'] . '" target="_blank">Emitir boleto</a>',
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($contaReceber['email'], $contaReceber['nome'])
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
