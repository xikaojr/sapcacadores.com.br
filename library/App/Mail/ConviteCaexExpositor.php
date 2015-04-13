<?php

class App_Mail_ConviteCaexExpositor {

    public static function enviar($dados) {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');
        $view = Zend_Registry::get('view');

        $caexTable = new CaexCentrosCustosTable();
        $table = new CaexConviteExpositorTable();
        $tablePessoasVinculo = new PessoaVinculoTable();
        $tablePessoas = new PessoaTable();

        // $caex = $caexTable->find($dados['caex_centro_custo_id'])->current();
        $caex = $caexTable->findByPessoaAndCentroCusto($dados['caex_centro_custo_id'], $dados['centro_custo_id']);
        $expositor = $table->find($dados['id'])->current()->toArray();

        $pessoas = $tablePessoasVinculo->find($caex['pessoa_vinculo_id'])->current()->toArray();
        $pessoa_juridica = $tablePessoas->find($pessoas['pessoa_juridica_id'])->current()->toArray();
        $pessoa_fisica = $tablePessoas->find($pessoas['pessoa_fisica_id'])->current()->toArray();

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $caex['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }

        $siglaIdioma = ($p['pais_id'] == $view->helperCodigoDoBrasil()) ? 'pt_br' : 'en';
        $arqEmail = 'evento/caex/expositor/convites/' . str_replace('.', '-', CLIENTE) . '/' . $centroCusto . '-' . $idioma . '.phtml';

        $params['tes.id'] = TiposEmails::CONVITECAEXEXPORSITOR;
        $params['idioma'] = $siglaIdioma;
        $params['centro_custo_id'] = $dados['centro_custo_id'];
        $rsConteudoEmails = App_Utilidades::getConteudoEmail($params);
        $rsConteudoEmails = end($rsConteudoEmails);
        //conteudo do texto
        $conteudoEmail = $rsConteudoEmails['corpo'];

        if (!empty($conteudoEmail)) {
            if (isset($expositor['email']) && !empty($expositor['email'])) {

// campos magicos
                $valores = array(
                    'expositor_nome' => $expositor['nome'],
                    'link' => $expositor['link'],
                    'pf_nome' => $pessoa_fisica['nome'],
                    'pj_nome' => $pessoa_juridica['nome'],
                    'razao_social' => $pessoa_juridica['razao_social']
                );

                $conteudoEmail = App_Filtro::camposMagicos($valores, $conteudoEmail);

                $mail = new App_Mail();
                $mail->assunto($translate->_($rsConteudoEmails['assunto']))
                        ->de($rsConteudoEmails['remetente']);

                if ($dados['enviarPara'] == 'true') {
                    $mail->para($dados['emailPara'], $expositor['nome']);
                } else {
                    $mail->para($expositor['email'], $expositor['nome']);
                }

                $mail->mensagem($conteudoEmail);


                $copia = $rsConteudoEmails['copia'];

                if (!empty($copia)) {
                    $copia = explode(',', $copia);

                    foreach ($copia as $c) {
                        $mail->copiaOculta($c);
                        //$mail->copiaOculta('wlisses@itarget.com.br');
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
