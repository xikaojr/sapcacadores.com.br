<?php

class App_Mail_CaexMontadora {

    public static function enviar($dados) {
        
        $translate = Zend_Registry::get('translate');
        $view = Zend_Registry::get('view');

        $table = new CaexTerceirizadosTable();
        $tableCredenciais = new CaexCredenciaisTable();
        $tableCaex = new CaexCentrosCustosTable();
        $tablePessoasVinculo = new PessoaVinculoTable();
        $tablePessoas = new PessoaTable();
        
        $terceirizado = $table->find($dados['id'])->current()->toArray();
        $pessoas = $tablePessoasVinculo->find($terceirizado['pessoa_vinculo_id'])->current()->toArray();
        $pessoa_juridica = $tablePessoas->find($pessoas['pessoa_juridica_id'])->current()->toArray();
        $pessoa_fisica = $tablePessoas->find($pessoas['pessoa_fisica_id'])->current()->toArray();
        
        $credencial = $tableCredenciais->find($terceirizado['caex_credencial_id'])->current()->toArray();
        $caex = $tableCaex->find($credencial['caex_centro_custo_id'])->current()->toArray();
        $pessoasCaex = $tablePessoasVinculo->find($caex['pessoa_vinculo_id'])->current()->toArray();
        $pessoa_fisica_caex = $tablePessoas->find($pessoasCaex['pessoa_fisica_id'])->current()->toArray();
        $pessoa_juridica_caex = $tablePessoas->find($pessoasCaex['pessoa_juridica_id'])->current()->toArray();

        $configuracaoTable = new ConfiguracaoTable();
        $centroCusto = $caex['centro_custo_id'];
        $linhas = $configuracaoTable->findAllByCentroCustoId($centroCusto);

        foreach ($linhas as $linha) {
            $configuracao[$linha['codigo']] = $linha;
        }
        
        $idioma = $dados['idioma'];
        $arqEmail = 'evento/caex/montagem-desmontagem/convites/' . str_replace('.', '-', CLIENTE) . '/' . $centroCusto . '-' . $idioma . '.phtml';
        
        if($idioma == 'pt-br')
            $assunto = $translate->_('Congresso - Convite Montadora');
        else
            $assunto = $translate->_('Congresso - Invited Assembler');
        
        if (is_file(MODULES_PATH . 'icongresso/views/scripts/' . $arqEmail)) {

            //if (isset($expositor['email']) && !empty($expositor['email'])) {
            if (true) {    

                $terceirizado['link'] = 'http://icongresso.' . CLIENTE . '/evento/' . $centroCusto . '/caex/montagem-desmontagem/hash/h/' . $terceirizado['hash_link_convite'] . '/lang/' . $idioma. '/t/' . base64_encode($terceirizado['id']);
                $view->terceirizado = $terceirizado;
                $view->pessoa_juridica = $pessoa_juridica;
                $view->pessoa_fisica = $pessoa_fisica;
                $view->pessoa_fisica_caex = $pessoa_fisica_caex;
                $view->pessoa_juridica_caex = $pessoa_juridica_caex;
                $view->logoRodape = 'http://icase.' . CLIENTE . '/images/clientes/' . CLIENTE . '/logo_membro_' . $centroCusto . '.jpg';

                $conteudoEmail = $view->render($arqEmail);
                $de = (isset($configuracao['173'])) ? $configuracao['173']['valor_referencia'] : '';

                $mail = new App_Mail();
                $mail->assunto($assunto)
                        ->de($de)
                        ->para($pessoa_fisica['email'], $pessoa_fisica['nome'])
                        ->mensagem($conteudoEmail);

                $copia = (isset($configuracao['172'])) ? $configuracao['172']['valor_referencia'] : '';

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