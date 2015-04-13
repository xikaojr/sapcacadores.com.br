<?php

class App_Mail_ConviteAutores {

    public static function enviar($dados) {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $trabalhosTable = new Trabalhos();
        $trabalhoGrade = new TrabalhosGrade();
        $trabalhosAutoresTable = new TrabalhosAutores();
        $p = $trabalhosTable->getByIdOrStatus($dados['trabalho_id'], $dados['trabalho_fase']);
        $grade = $trabalhoGrade->findByTrabalhoIdCentroCusto($dados['trabalho_id'], $dados['centro_custo_id']);

        if ($p['nota_final'] < 7) {
            throw new Exception($translate->_('A nota é menor do que 7'));
        }


        $configuracaoTable = new Configuracao();
        $centroCusto = $dados['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        if ($trabalhosTable->getTotalVagasConvidadoPreenchidas($dados['trabalho_id'], $dados['centro_custo_id']) >= $configuracao[186]['valor_referencia']) {
            throw new Exception($translate->_('O total de vagas já foi preenchido.'));
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';

        $params['tes.id'] = TiposEmails::CONVITEAUTORES;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
//conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($p['email']) && !empty($p['email'])) {

                $hash = sha1(date('dmYHisu') . rand(0, 9));
                $valores = array(
                    'pessoa_nome' => $p['pessoa_nome'],
                    'email' => $p['email'],
                    'trabalho_numero_sae' => $p['trabalho_numero_sae'],
                    'titulo_lng' => $p['titulo_lng'],
                    'nota_final' => $p['nota_final'],
                    'observacao_final' => $p['observacao_final'],
                    'trabalho_arquivo' => $p['nome_arquivo'],
                    'hora_apresentacao' => $grade['hora_ini'],
                    'data_apresentacao' => App_Date::formatar($grade['data']),
                    'local_apresentacao' => $grade['local'],
                    'link' => "http://icongresso." . CLIENTE . "/evento/{$centroCusto}/convidado-autores/cadastro/hash/h/" . $hash . "/t/" . base64_encode($p['id']),
                    'status' => App_Status::trabalhos($p['status']),
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();

                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente'])
                        ->para($p['mail'], $p['pessoa_nome'])
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
