<?php

class App_Filtro {

    /**
     * Formata um valor para que seja exibido na tela
     * @param double $valor Valor a ser formatado
     * @return string
     */
    public static function removerAcentos($var) {
        $from = array('À', 'Á', 'Ã', 'Â', 'É', 'Ê', 'Í', 'Ó', 'Õ', 'Ô', 'Ú', 'Ü', 'Ç', 'à', 'á', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'õ', 'ô', 'ú', 'ü', 'ç');
        $to = array('A', 'A', 'A', 'A', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'C', 'a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c');

        return str_replace($from, $to, $var);
    }

    /* Quando o usuario deixa o campo vazio nao atualiza o campo, por conta de uma regra que e chamada
     * dentro da função save, por isso foi criado essa regra
     * obs.:quando tiramos essa regra deu error na geração do boleto.Deixo claro que devemos corrigir esse erro,
     * mas como o tempo esta curto fiz esta funcao.
     */

    public static function ajusteLimpaCampos(array $campos, array $camposChaves) {

        foreach ($camposChaves as $campo) {
            $campos[$campo] = isset($campos[$campo]) ? trim($campos[$campo]) : '';
            if (empty($campos[$campo])) {
                $campos[$campo] = ' ';
            }
        }
        return $campos;
    }

    /**
     * Subistitui os campos/chaves pelo conteudo
     * @param array $campos
     * @param string $conteudo
     * @return string
     */
    public static function camposMagicos($campos, $conteudo) {
        $novoConteudo = $conteudo;

        foreach ($campos as $chave => $valor) {
            $campoMagico = "{{" . $chave . "}}";

            if ($valor == 1 && strlen($valor) == 1) {
                $novoConteudo = str_replace('{{' . $chave . '}}', '', $novoConteudo);
                $novoConteudo = str_replace('{{/' . $chave . '}}', '', $novoConteudo);
            } else {
                if (!empty($valor))
                    $novoConteudo = str_replace($campoMagico, $valor, $novoConteudo);
            }
        }

        $novoConteudo = preg_replace('/{{(.*?)}}(.*?){{\/(.*?)}}/i', '', $novoConteudo);
        $novoConteudo = preg_replace('/{{(.*?)}}/i', '', $novoConteudo);
        
        return $novoConteudo;
    }

    /**
     * Campos magicos utilizado nos modelos de 
     * contrato,devolucao,transferencia....
     * @param String $conteudo
     * @param Array $valores
     * @return String
     */
    public static function camposMagicosCaptacao($conteudo, array $valores) {
        $translate = Zend_Registry::get('translate');
        $captacao = $valores['captacao'];
        $contaReceber = $valores['conta_receber'];
        $centro_custo = $valores['centro_custo'];

        $conta_receber = '';
        $data_vencimento = '';

        if (count($contaReceber)) {
            foreach ($contaReceber as $v) {
                $conta_receber.= $translate->_('parcela:') . $v['num_parcela'] . ' ' .
                        $translate->_('vencimento:') . App_Date::ptBr($v['data_vencimento'], false) . ' ' .
                        $translate->_('valor:') . App_Moeda::formatar($v['valor']) . '(' . App_Moeda::porExtenso($v['valor']) . ')' . '<br />';
                $data_vencimento.= App_Date::ptBr($v['data_vencimento'], false) . '<br />';
            }
        }

        $campos = array(
            'codigo_contrato' => $captacao['id'],
            'empresa_razao_social' => $captacao['empresa_cobranca_descricao'],
            'empresa_nome_fantasia' => $captacao['empresa_cobranca_nome_fantasia'],
            'contato_endereco' => strtolower($captacao['cobr_logradouro']),
            'contato_numero' => $captacao['cobr_numero'],
            'contato_complemento' => strtolower($captacao['cobr_complemento']),
            'contato_cidade' => strtolower($captacao['cobr_municipio']),
            'contato_cep' => $captacao['cobr_cep'],
            'contato_uf' => strtolower($captacao['cobr_uf']),
            'contato_pais' => strtolower($captacao['cobr_pais']),
            'empresa_cnpj' => App_Utilidades::mask($captacao['cnpj_cobranca'], '99.999.999.9999/99'),
            'empresa_ie' => $captacao['ie_cobranca'],
            'empresa_im' => $captacao['im_cobranca'],
            'contato_nome' => $captacao['rep_leg_nome'],
            'contato_cargo' => $captacao['rep_leg_cargo'],
            'contato_rg' => $captacao['rep_leg_rg'],
            'contato_cpf' => App_Utilidades::mask($captacao['rep_leg_cpf'], '999.999.999-99'),
            'contato_nome_2' => $captacao['rep_leg2_nome'],
            'contato_cargo_2' => $captacao['rep_leg2_cargo'],
            'contato_rg_2' => $captacao['rep_leg2_rg'],
            'contato_cpf_2' => App_Utilidades::mask($captacao['rep_leg2_cpf'], '999.999.999-99'),
            'valor_negociado' => App_Moeda::formatar($captacao['valor_entrada']),
            'valor_por_extenso' => App_Moeda::porExtenso($captacao['valor_entrada']),
            'quantidade_parcelas' => $captacao['num_parcelas'],
            'numero_estante' => '',
            'area_objeto_negociado' => $captacao['area_objeto_negociado'],
            'nome_termo_comp' => $captacao['rep_leg_nome'],
            'rg_termo_comp' => $captacao['rep_leg_rg'],
            'cpf_termo_patr' => App_Utilidades::mask($captacao['rep_leg_cpf'], '999.999.999-99'),
            'cargo_termo_comp' => $captacao['rep_leg_cargo'],
            'nome_termo_comp_2' => $captacao['rep_leg2_nome'],
            'rg_termo_comp_2' => $captacao['rep_leg2_rg'],
            'cpf_termo_patr_2' => App_Utilidades::mask($captacao['rep_leg2_cpf'], '999.999.999-99'),
            'cargo_termo_comp_2' => $captacao['rep_leg2_cargo'],
            'razao_social' => $captacao['empresa_cobranca_descricao'],
            'beneficios' => $captacao['objeto_negociacao'],
            'conta_receber' => $conta_receber,
            'contato_fone' => $captacao['fone_cobranca'],
            'contato_fax' => $captacao['fax_cobranca'],
            'contato_email' => $captacao['email_cobranca'],
            'contato_principal_nome' => $captacao['con_emp_nome'],
            'contato_principal_email' => $captacao['con_emp_email'],
            'contato_principal_cpf' => $captacao['con_emp_cpf'],
            'contato_principal_endereco' => strtolower($captacao['logradouro']),
            'contato_principal_numero' => $captacao['numero'],
            'contato_principal_cep' => $captacao['cep'],
            'contato_principal_bairro' => strtolower($captacao['bairro']),
            'contato_principal_cidade' => strtolower($captacao['municipio']),
            'contato_principal_uf' => strtolower($captacao['uf']),
            'contato_principal_tipo_logradouro' => strtolower($captacao['tipo_logradouro']),
            'data_vencimento' => $data_vencimento,
            'nome_evento' => $centro_custo['descricao'],
            'centro_custo' => $centro_custo['id']
        );

        return App_Filtro::camposMagicos($campos, $conteudo);
    }

    public static function camposMagicosCartaAssociadoEmDia($conteudo, array $valores) {
        $campos = array(
            'nome' => $valores['nome'],
            'cpf' => $valores['cpf'],
            'categoria' => $valores['categoria'],
            'data_extenso' => $valores['data_extenso'],
            'cobranca' => $valores['cobranca'],
            'matricula' => $valores['matricula'],
        );

        return App_Filtro::camposMagicos($campos, $conteudo);
    }
    
    public static function somenteNumeros($string){
        return preg_replace("$[^0-9]$", "", $string);
    }

}
