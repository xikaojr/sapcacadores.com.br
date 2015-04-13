<?php

class App_Mail_ConfirmarCadastro {

    public static function enviar($dados) {

        $translate = Zend_Registry::get('translate');

        if (is_file($dados['modelo'])) {

            if (isset($dados['email']) && !empty($dados['email'])) {

                $conteudo = file_get_contents($dados['modelo']);

                $campos = array(
                    'nome' => $dados['nome'],
                    'email' => $dados['email'],
                    'logo' => 'http://' . $_SERVER['HTTP_HOST'] . '/images/clientes/' . CLIENTE . '/logoP.png',
                    'link' => isset($dados['link']) ? "<a href='http://{$_SERVER['HTTP_HOST']}{$dados['link']}' target='_blank'>link</a>" : ''
                );

                $conteudoEmail = App_Filtro::camposMagicos($campos, $conteudo);

                $mail = new App_Mail();

                $subject = 'Confirmação de cadastro';

                $mail->assunto($subject)
                        ->de('no-reply@' . $_SERVER['HTTP_HOST'])
                        ->para($dados['email'], $dados['nome'])
                        // ->para('edilson@itarget.com.br','edilson')
                        ->mensagem($conteudoEmail);

                return $mail->enviar();
            } else {
                throw new Exception($translate->_('Pessoa nao tem email'));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido em ' . str_replace('.', '-', CLIENTE)) . '. ');
        }
    }

}
