<?php

class App_Mail_ConviteAvaliador {

    public static function enviar($dados) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $avaliadorTable = new AvaliadorCentroCusto();
        $p = $avaliadorTable->findByPessoaAndCentroCusto($dados['pessoa_id'], $dados['centro_custo_id']);

        $centroCusto = $dados['centro_custo_id'];

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'pt_br';

        $params['tes.id'] = TiposEmails::CONVITEAVALIADOR;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            $link_espaco = Zend_Registry::get('config')->link_espaco;
            $cliente = !empty($link_espaco) ? $link_espaco : CLIENTE;
            if (isset($p['pessoa_email']) && !empty($p['pessoa_email'])) {
                $valores = array();
                $valores['link'] = 'icongresso.' . $cliente . '.itarget.com.br/evento/' . $centroCusto . '/avaliador/cadastro/hash/h/' . $p['hash_link_convite'];
                //$valores['logo_rodape'] = '<img src="http://icase.' . $cliente . '/images/clientes/' . CLIENTE . '/logo_avaliador_' . $centroCusto . '.jpg" />';
                $valores['pessoa_nome'] = $p['pessoa_nome'];
                $valores['pessoa_email'] = $p['pessoa_email'];

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($rsConteudoEmails['assunto'])
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
