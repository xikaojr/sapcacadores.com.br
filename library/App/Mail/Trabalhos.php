<?php

class App_Mail_Trabalhos {

    public static function emailEnvioResumo(array $params) {

        $rsConteudoEmails = end(App_Utilidades::getConteudoEmail($params));

        if (!empty($rsConteudoEmails)) {

            $pessoa = $params['pessoa'];

            $paramsTrab = array();
            $paramsTrab['autor_principal_nome'] = $params['autor_principal_nome'];
            $paramsTrab['email_autor'] = $pessoa['email'];
            $paramsTrab['trabalho_titulo_ingles'] = $params['resumo']['titulo_lng'];
            $paramsTrab['trabalho_titulo'] = $params['resumo']['titulo'];
            $paramsTrab['link_senha'] = 'http://icongresso.' . CLIENTE . '.itarget.com.br/inscricao/auth/esqueci-minha-senha/centro-custo/' . $params['centro-custo'];
            $paramsTrab['link_icongresso'] = 'http://icongresso.' . CLIENTE . '.itarget.com.br/inscricao/auth/index/centro-custo/' . $params['centro-custo'];
            $paramsTrab['logo_rodape'] = 'http://icongresso.' . CLIENTE . '/images/clientes/' . CLIENTE . '/' . $params['centro-custo'] . '/logo.png';

            //conteudo do texto

            $conteudoEmail = App_Filtro::camposMagicos($paramsTrab, $rsConteudoEmails['corpo']);

            $de = $rsConteudoEmails['remetente'];

            $mail = new App_Mail();
            $mail->assunto($rsConteudoEmails['assunto'])
                    ->de($de);

            if (!empty($pessoa['email']) && !empty($pessoa['email_pessoal'])) {
                $mail->para($pessoa['email'])->copia($pessoa['email_pessoal']);
            } else if (!empty($pessoa['email'])) {
                $mail->para($pessoa['email']);
            } else if (!empty($pessoa['email_pessoal'])) {
                $mail->para($pessoa['email_pessoal']);
            }

            $mail->mensagem($conteudoEmail);

            foreach (explode(',', $rsConteudoEmails['copia']) as $p) {
                if (!empty($p)) {
                    $mail->copiaOculta($p);
                }
            }

            $mail->enviar();
        } else {
            throw new Zend_Mail_Exception('O conteudo do e-mail nao foi definido');
        }
    }
}
