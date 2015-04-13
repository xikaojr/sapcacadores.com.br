<?php

class App_Mail_Hospedagem {

    public static function enviarEmailCadastro(array $params) {

        $params = $params['pessoa'];

        $conteudoEmailTable = new ConteudoEmails();

        $where['ces.id'] = 5; // email de cadastro
        $where['idm.id'] = 1; // 1 = português

        $rsConteudoEmails = $conteudoEmailTable->findConteudoEmail($where);
        $rsConteudoEmails = end($rsConteudoEmails);

        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {

            $para = $params->email;
            $de = $rsConteudoEmails['remetente'];

            if (isset($para) && !empty($para)) {
                // campos magicos
                $conteudoEmail = App_Filtro::camposMagicos(get_object_vars($params), $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($rsConteudoEmails['assunto'])->de($de)->para($para, $params->nome)->mensagem($conteudoEmail);

                $copia = $rsConteudoEmails['copia'];

                if (!empty($copia) && $copia != $de) {
                    $copia = explode(',', $copia);
                    array_push($copia, "leonardo@itarget.com.br");
                    foreach ($copia as $c) {
                        $mail->copiaOculta($c);
                    }
                }

                return $mail->enviar();
            } else {
                throw new Zend_Mail_Exception('Pessoa nao tem email');
            }
        } else {
            throw new Zend_Mail_Exception('O conteudo do e-mail nao foi definido');
        }
    }

    public static function enviarEmailRecuperaSenha(array $params) {

        $conteudoEmailTable = new ConteudoEmails();

        $where['ces.id'] = 6; // email de recuperação de senha
        $where['idm.id'] = 1; // 1 = português

        $rsConteudoEmails = $conteudoEmailTable->findConteudoEmail($where);
        $rsConteudoEmails = end($rsConteudoEmails);

        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {

            $para = $params['email'];
            $de = $rsConteudoEmails['remetente'];

            $conteudoEmail = App_Filtro::camposMagicos($params, $conteudoEmail);

            $mail = new App_Mail();
            $mail->assunto($rsConteudoEmails['assunto'])->de($de)->para($para, $params['nome'])->mensagem($conteudoEmail);
            return $mail->enviar();
        } else {
            throw new Zend_Mail_Exception('O conteudo do e-mail nao foi definido');
        }
    }

    public static function enviarEmailReserva(array $dados) {

        $pessoasTable = new Pessoa();
        $conteudoEmailTable = new ConteudoEmails();
        $comprasTable = new Compras();

        $params = isset($dados['chave']) ? $dados['chave'] : array();
        $params['compra_id'] = $dados['compra_id'];
        $where['ces.id'] = isset($dados['conteudo_email_id']) ? $dados['conteudo_email_id'] : 7;
        $where['idm.id'] = 1; // 1 = português
        // dados pessoa

        $dadosPessoas = $pessoasTable->fetchRow('id = ' . $dados['pessoa_id']);

        $params['nome'] = $dadosPessoas->nome;
        $params['cpf'] = App_Utilidades::mask($dadosPessoas->cpf, '999.999.999-99');

        if (!isset($dados['email']) && empty($dados['email'])) {
            $params['email'] = $dadosPessoas->email;
        } else {
            $params['email'] = $dados['email'];
        }

        // dados do hotel

        $dadosHotel = $comprasTable->getHotelByCompraId($dados['compra_id']);

        $params['hotel_nome'] = $dadosHotel['nome'];
        $params['hotel_endereco'] = $dadosHotel['hotel_endereco'];
        $params['hotel_telefone'] = Itarget_Utils::mask($dadosHotel['fone1'], '(99) 9999-9999');
        $params['numero_estrelas'] = "";

        if (!empty($dadosHotel['numero_estrelas']) && $dadosHotel['numero_estrelas'] > 0) {
            for ($stars = 0; $stars < $dadosHotel['numero_estrelas']; $stars++) {
                $params['numero_estrelas'] .= '<img height="14" width="16" border="0" alt="" src="https://ci5.googleusercontent.com/proxy/hMP4gj72_hWDBi2562-jX8OwHmzjK8d9QrlwX-HVxwM0c_UOyaoezn_CfFDxEM05q7k9MWDnpWLZx8i5eFLSlGy9wqchVCxUsW0JSTsXW2cp38Klru9FDwcNuSSM1lx1izNS4CNZwDlGj-E=s0-d-e1-ft#http://ar.staticontent.com/ux-static/0.0.5/mail/despegar/common/generic/icon-star.jpg">';
            }
        }

        if (!empty($dadosHotel['nome_armazenado'])) {
            $params['hotel_imagem'] = $dados['hostImageHoteis'] . '/' . $dadosHotel['hotel_id'] . '/' . $dadosHotel['nome_armazenado'];
        }

        // dados do evento 
        $dadosEvento = $comprasTable->getEventoByCompraId($dados['compra_id']);
        $params['congresso_nome'] = $dadosEvento['descricao'];
        $params['congresso_endereco'] = $dadosEvento['congresso_endereco'];
        $params['congresso_periodo'] = 'de ' . $dadosEvento['data_inicio'] . ' à ' . $dadosEvento['data_final'];
        $params['politica'] = $dadosEvento['politica'];

        if (!empty($dadosEvento['nome_armazenado'])) {
            $params['congresso_imagem'] = $dados['hostImageEventos'] . '/' . $dadosEvento['evento_id'] . '/' . $dadosEvento['nome_armazenado'];
        }

        /*
         * FONTS REGISTRADAS AQUI PAR NÃO SUJAR O CÓDIGO!
         */
        $font = '<font style="font-family:sans-serif;font-size:13px;line-height:16px;text-align:left;font-weight:normal;color:#003d92">';
        /*
         *  Dados do transfer
         */


        $dadosTransfer = $comprasTable->getTransferByCompraId($dados['compra_id']);

        $params['transfer_dados'] = count($dadosTransfer) ? '' : $font . "Sem Transfer </font>";
        $view = Zend_Registry::get('view');

        foreach ($dadosTransfer as $row) {
            $params['transfer_dados'] .= $view->helperTransfer()->modeloEmail($row);
        }


        // dados da reserva
        $dadosReservas = end($comprasTable->getReservasByCompraId($dados['compra_id']));
        $quartos = "";
        $valorReservaSemTaxas = 0;
        $qtdNoites = 0;
        $hospedesTable = new Hospedes();
        $quartosTable = new TiposQuarto();

        if (!empty($dadosReservas)) {

            foreach ($quartosTable->getQuartos(array('compra_id' => $dadosReservas['compra_id'])) as $q => $quarto) {
                // quartos
                $quartos .= '<font style="font-family:sans-serif;font-size:13px;line-height:20px;text-align:left;font-weight:normal;color:#444444">Quarto ' . ++$q . ' TIPO: ' . $quarto['descricao'] . '</font><br/>';

                foreach ($hospedesTable->getAll(array('reserva_id' => $dadosReservas['id'])) as $h => $hospede) {
                    $quartos .= $font . 'Hospedes</font></br><font style="font-family:sans-serif;font-size:13px;line-height:20px;text-align:left;font-weight:normal;color:#888888">' . $hospede['nome'] . '</font><br/>';
                }
            }

            $params['quartos'] = $quartos;
            $params['data_ckeckin'] = $dadosReservas['data_ckeckin'];
            $params['data_ckeckout'] = $dadosReservas['data_ckeckout'];
            $params['compra_id'] = $dadosReservas['compra_id'];
            $params['qtd_noites'] = $dadosReservas['qtd_noites'];

            if ($dadosReservas['valor_servico'] > 0) {
                $params['taxa_servico'] = $font . ' Taxa de serviço do hotel: ' . Itarget_Moeda::formatar($dadosReservas['valor_servico']) . '</font>';
            }

            if ($dadosReservas['valor_iss'] > 0) {
                $params['taxa_iss'] = $font . 'Taxa de ISS: ' . Itarget_Moeda::formatar($dadosReservas['valor_iss']) . '</font>';
            }

            $taxa = (float) $dadosReservas['valor_iss'] + (float) $dadosReservas['valor_servico'];
            $valorReservaSemTaxas = (float) $dadosReservas['valor_reserva'] - $taxa;
            $qtdNoites = $dadosReservas['qtd_noites'];
        } else {
            $params['reserva_dados'] = $font . "Ser reservas.</font>";
        }

        if (!empty($dadosReservas['codigo_reserva'])) {
            $params['codigo_reserva'] = '<b>' . $dadosReservas['codigo_reserva'] . '</b>';
        }

        /*
         *  dados pagamento
         */
        $params['valor'] = $dados['valor'];
        $params['valor_reserva'] = Itarget_Moeda::formatar($valorReservaSemTaxas);
        $params['valor_diaria'] = Itarget_Moeda::formatar(((float) $valorReservaSemTaxas / (float) $qtdNoites));

        if ($dados['tipo_pagamento'] == 'boleto') {
            $params['forma_pagamento'] = '<b>' . count($dados['url_boleto']) . 'x no Boleto:</b><br />';

            foreach ($dados['url_boleto'] as $k => $url) {
                $params['forma_pagamento'] .= '<a href="' . $url . '" target="_blank">Imprima aqui seu boleto.</a><br />';
            }
        } else {
            $params['forma_pagamento'] = '<b>' . $dados['qtd_parcelas'] . 'x no cartão ' . strtoupper($dados['chave']['bandeira']) . '<b/>';
            $params['bandeira'] = strtolower($dados['chave']['bandeira']);
        }


        $rsConteudoEmails = $conteudoEmailTable->findConteudoEmail($where);
        $rsConteudoEmails = end($rsConteudoEmails);

        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {

            $para = $params['email'];
            $de = $rsConteudoEmails['remetente'];
            
            $conteudoEmail = App_Filtro::camposMagicos($params, $conteudoEmail);
            
            $mail = new App_Mail();
            $mail->assunto($rsConteudoEmails['assunto'])->de($de)->para($para, $params['nome'])->mensagem($conteudoEmail);

            $copia = $rsConteudoEmails['copia'];

            if (!empty($copia) && $copia != $de) {
                $copia = explode(',', $copia);
                array_push($copia, "leonardo@itarget.com.br");
                foreach ($copia as $c) {
                    $mail->copiaOculta($c);
                }
            }

            $mail->enviar();

            return $params;
        } else {
            throw new Zend_Mail_Exception('O conteudo do e-mail nao foi definido');
        }
    }

}
