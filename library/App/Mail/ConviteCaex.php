<?php

class App_Mail_ConviteCaex {

    public static function enviar($dados) {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $caexTable = new CaexCentrosCustosTable();
        $caex = $caexTable->findByPessoaAndCentroCusto($dados['id'], $dados['centro_custo_id']);

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($dados['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::CONVITECAEX;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($caex['email']) && !empty($caex['email'])) {

                // campos magicos
                $valores = array(
                    'link_cadastro' => 'http://icongresso.' . CLIENTE . '/evento/' . $centroCusto . '/caex/cadastro/hash/h/' . $caex['hash_link_convite'] . '/lang/' . $siglaIdioma,
                    'link_caex' => $view->pessoa['pessoa_email'],
                    'nome' => $caex['nome'],
                );

                $view->caex = $caex;
                
                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($caex['email'], $caex['nome'])
                        ->mensagem($conteudoEmail);

                $copia = $rsConteudoEmails['copia'];
                
                if (!empty($copia)) {
                    $copia = explode(',', $copia);

                    foreach ($copia as $c) {
                        //$mail->copiaOculta('acrisio@itarget.com.br'); 
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
