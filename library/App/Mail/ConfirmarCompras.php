<?php

class App_Mail_ConfirmarCompras {

    public static function enviar($dados) {

        $translate = Zend_Registry::get('translate');
        $view = Zend_Registry::get('view');

        $idioma = 'pt-br';

        $arqEmail = 'ecommerce/modelo-email/' . str_replace('.', '-', CLIENTE) . '/' . $idioma . '-confirmacao-compra.phtml';

        if (is_file(MODULES_PATH . 'default/views/scripts/' . $arqEmail)) {

            if (isset($dados['email']) && !empty($dados['email'])) {

                $conteudo = file_get_contents(MODULES_PATH . 'default/views/scripts/' . $arqEmail);
                
                // produtos -----------------
                
                $html = null;
                
                foreach($dados['produtos'] as $produto){
                    $html .= '<p>'.$produto['aga_descricao'].'</p>';
                }
                
                
                $campos = array(
                    'nome' => $dados['nome'],
                    'email' => $dados['email'],
                    'link_boleto' => $dados['link_boleto'],
                    'produtos' => $html,
                    'logo' => 'http://'.$_SERVER['HTTP_HOST'].'/images/clientes/' . CLIENTE . '/logoP.png'
                );

                $conteudoEmail = App_Filtro::camposMagicos($campos, $conteudo);

                $mail = new App_Mail();

                $subject = 'Confirmação de compra';

                $mail->assunto($subject)
                        ->de('no-reply@' . $_SERVER['HTTP_HOST'])
                        ->para($dados['email'], $dados['nome'])
                        //->para('wlissesbb@gmail.com', $p['pessoa_nome'])
                        ->mensagem($conteudoEmail);

                return $mail->enviar();
            } else {
                throw new Exception($translate->_('Pessoa nao tem email'));
            }
        } else {
            throw new Exception($translate->_('O conteudo do e-mail nao foi definido em '.str_replace('.', '-', CLIENTE)).'. ');
        }
    }

}
