<?php

class App_Mail_ConviteCoAutor {

    public static function enviar($dados) {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

        $centroCusto = $dados['centro_custo_id'];

        $params['tes.id'] = TiposEmails::CONVITECOAUTOR;
        $params['idioma'] = $dados['idioma'];
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = end(App_Utilidades::getConteudoEmail($params));
        
        //conteudo do texto

        if ($rsConteudoEmails) {
            $conteudoEmail = $rsConteudoEmails['corpo'];
            if (isset($dados['email']) && !empty($dados['email'])) {

                $link = 'http://icongresso.' . CLIENTE . '.itaret.com.br/inscricao/coautor/cadastro/centro-custo/' . $centroCusto . '/hash/h/' . $dados['hash_link_convite'];
                
                $valores = array(
                    'link_co_autor' => '<a href="' . $link . '">' . $link . '</a>',
                    'nome_convidado' => $dados['nome'],
                    'nome_autor_principal' => $dados['pss_nome'],
                    'titulo_paper' => $dados['titulo_lng'],
                    'email_convite' => $dados['email']
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);
//------------------------------------ email -----------------------------------               
                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($dados['email'], $dados['nome'])
                        ->mensagem($conteudoEmail);

                $copia = $rsConteudoEmails['copia'];

                if (!empty($copia)) {
                    $copia = explode(',', $copia);
                    foreach ($copia as $c) {
                        $mail->copiaOculta($c);
                    }
                }

                return $mail->enviar();
//------------------------------------------------------------------------------                
            } else {
                throw new Exception($translate->_('Pessoa nao tem email'));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido'));
        }
    }

}
