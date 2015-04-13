<?php

class App_Mail_NumeracaoSae {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');
        $trabalhoTable = new Trabalhos();
        $p = $trabalhoTable->getById($dados['trabalho_id']);

        $configuracaoTable = new Configuracao();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::NUMERACAOSAE;
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
                    'pessoa_nome' => $view->pessoa['pessoa_nome'],
                    'trabalho_numero_sae' => $view->pessoa['trabalho_numero_sae'],
                    'link' => 'http://icongresso.' . CLIENTE . '/evento/' . $centroCusto . '/avaliador'
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
