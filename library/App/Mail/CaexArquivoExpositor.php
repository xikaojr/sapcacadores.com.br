<?php

class App_Mail_CaexArquivoExpositor {

    public static function enviar($dados) {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $caexConviteExpositoresTable = new CaexConviteExpositor();

        $conviteExpositor = $caexConviteExpositoresTable->find($dados['id'])->current()->toArray();

        $configuracaoTable = new ConfiguracaoTable();
        $linhas = $configuracaoTable->findAllByCentroCustoId($dados['centro_custo_id']);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $idioma = $dados['idioma'] == 'pt-br' ? 'pt_br' : $dados['idioma'];

        $params['tes.id'] = TiposEmails::CAEXARQUIVOEXPOSITOR;
        $params['idioma'] = $idioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);

//conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($conviteExpositor['email']) && !empty($conviteExpositor['email'])) {
                // campos magicos
                $valores = array(
                    'link' => 'http://icongresso.' . CLIENTE . '/formulario_de_inscricao_expositores_b.pdf'
                    , 'nome' => $conviteExpositor['nome']
                    , 'email' => $conviteExpositor['email']
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($conviteExpositor['email'], $conviteExpositor['nome'])
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
