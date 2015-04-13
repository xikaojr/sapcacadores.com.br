<?php

class App_Mail_Cobranca {

    public static function enviar($contaReceberId) {
        $translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $contaReceberTable = new ContaReceber();
        $contaReceber = $contaReceberTable->findById($contaReceberId);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $contaReceber['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $idioma = (isset($contaReceber['pais_id']) && $contaReceber['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::COBRANCA;
        $params['idioma'] = $idioma;
        $params['centro_custo_id'] = $centroCusto;
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {

            if (isset($contaReceber['email']) && !empty($contaReceber['email'])) {

                $mail = new App_Mail();

                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente']);

                if (isset($contaReceber['empresa']) && !empty($contaReceber['empresa'])) {
                    $pss_nome = $contaReceber['empresa'];
                    $mail->para($contaReceber['email_empresa'], $contaReceber['empresa']);
                } else {
                    $pss_nome = $contaReceber['nome'];
                    $mail->para($contaReceber['email'], $contaReceber['nome']);
                }

                $valores = array(
                    'pessoa_nome' => $pss_nome,
                    'data_vencimento' => App_Helper_FormataDate::formataDateStatic($contaReceber['data_vencimento'], 'Y-m-d'),
                    'valor' => App_Helper_Moeda::formatar($contaReceber['valor']),
                    'boleto' => '<a href="http://' . $_SERVER['SERVER_NAME'] . '/default/boleto/imprimir/numero/' . $contaReceber['num_nosso_numero'] . '/layout/' . $contaReceber['controle_layout_id'] . '" target="_blank">Emitir boleto</a>'
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);
                $mail->mensagem($conteudoEmail);

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
