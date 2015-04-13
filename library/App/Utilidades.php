<?php

class App_Utilidades {

    /**
     * Retorna a forma de pagamento
     * @param string $s tipo forma pagamento
     * @param type $tipoRetorno se vai retorna linha ou array
     * @return string/array
     */
    public static function formaPagamento($s, $tipoRetorno = 'row') {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

        $data = array();
        $data[0] = $translate->_('Ainda nao definido');
        $data[1] = $translate->_('Boleto');
        $data[2] = $translate->_('Cartao de credito');
        $data[3] = $translate->_('Deposito');

        if ($tipoRetorno == 'row') {
            return $data[$s];
        } else {
            return $data;
        }
    }

    /**
     * Formata o valor conforme mascara
     * @param string $val valor a ser mascarado
     * @param string $mask mascara
     * @return string
     */
    public static function mask($val, $mask) {

        if ($val == "")
            return '';

        if ($mask == 'valor') {
            $val = number_format($val, 2, ",", ".");

            return "R$ " . $val;
        }

        if ($mask == 'data') {
            $val = explode('-', $val);

            return $val[2] . '/' . $val[1] . '/' . $val[0];
        }

        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '9') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    /**
     * Verifica se um determinado centro de custo eh evento
     * @param int $centroCusto
     * @return boolean
     */
    public static function isCentroCustoEvento($centroCusto) {
        $centroCusto = (int) $centroCusto;

        $centroCustoTable = new CentroCusto();
        $linha = $centroCustoTable->findById($centroCusto);

        if ($linha && $linha['evento'] == 'S') {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param integer $codigo codigo de configuracao
     * @param string $valueCampo valor do campo
     * @return string/array
     */
    public static function getNomeCampoConfiguracoes($codigo, $valueCampo) {

        $configuracoesTable = new Configuracao();
        $configuracao = end($configuracoesTable->findAllByCodigo($codigo));
        $opcoes = explode("\n", $configuracao['valor_referencia']);
        $value = null;

        foreach ($opcoes as $opc) {
            $opcao = explode('|', $opc);
            if (trim($opcao[0]) == trim($valueCampo)) {
                $value = $opcao[1];
            }
        }

        if ($value == null) {
            $value = explode(',', $valueCampo);
            if ($value[0] == "99") {
                return $value[1];
            } else {
                return $valueCampo;
            }
        } else {
            return $value;
        }
    }

    public static function camposAssociados() {
        $campos = array();

        $campos['razao_social'] = array(
            'alias' => 'pv,pemp',
            'campo' => 'pemp.razao_social',
            'nome' => 'Razao Social',
            'function' => null
        );
        $campos['nome_fantasia'] = array(
            'alias' => 'pv,pemp', // ALIAS
            'campo' => 'pemp.nome', // CAMPO PRO
            'nome' => 'Nome Fantasia',
            'function' => null
        );

//        $campos['company_rep'] = array(
//            'alias' => 'pv,pemp',
//            'campo' => "case when pv.company_rep = 'S' then 'Sim' else 'Nao' end ",
//            'nome' => 'Company resp',
//            'function' => null
//        );
//        $campos['school_rep'] = array(
//            'alias' => 'pv,pemp',
//            'campo' => "case when pv.school_rep = 'S' then 'Sim' else 'Nao' end",
//            'nome' => 'School resp',
//            'function' => null
//        );

        $campos['nome'] = array(
            'alias' => '',
            'campo' => 'va.nome',
            'nome' => 'Nome',
            'function' => null
        );
        $campos['cpf'] = array(
            'alias' => '',
            'campo' => 'va.cpf',
            'nome' => 'CPF',
            'function' => 'App_Utilidades::mask',
            'formato' => '999.999.999-99'
        );
        $campos['email'] = array(
            'alias' => '',
            'campo' => 'va.email',
            'nome' => 'Email',
            'function' => null
        );
        $campos['numero_conselho'] = array(
            'alias' => '',
            'campo' => 'va.numero_conselho',
            'nome' => 'CRM',
            'function' => null
        );
        $campos['uf_conselho'] = array(
            'alias' => '',
            'campo' => 'va.uf_conselho',
            'nome' => 'CRM - UF',
            'function' => null
        );
        $campos['cat_profissional'] = array(
            'alias' => 'cp',
            'campo' => 'cp.descricao',
            'nome' => 'Cat. Profissional',
            'function' => null
        );
        $campos['area_interesse'] = array(
            'alias' => '',
            'campo' => "
                array_to_string(array(SELECT ai.descricao::TEXT FROM areas_interesse ai
                    INNER JOIN pessoas_areas_interesse pai2 ON pai2.area_interesse_id = ai.id
                    WHERE pai2.pessoa_id = va.pessoa_id),', '
                )    
            ",
            'nome' => 'Areas de interesses',
            'function' => null
        );
        $campos['area_atuacao'] = array(
            'alias' => '',
            'campo' => "
                array_to_string(array(SELECT aa.descricao::TEXT FROM areas_atuacao aa
                    INNER JOIN pessoas_areas_atuacao paa2 ON paa2.area_atuacao_id = aa.id
                    WHERE paa2.pessoa_id = va.pessoa_id),', '
                )    
            ",
            'nome' => 'Areas de atuacao',
            'function' => null
        );
        $campos['classificacoes'] = array(
            'alias' => '',
            'campo' => "
                array_to_string(array(SELECT c.descricao::TEXT FROM classificacoes c
                    INNER JOIN classificacoes_empresa ce ON ce.classificacao_id = c.id
                    INNER JOIN pessoas_vinculo pv ON pv.pessoa_juridica_id = ce.pessoa_id
                    WHERE pv.pessoa_fisica_id = va.pessoa_id),', '
                )    
            ",
            'nome' => 'Classificacoes',
            'function' => null
        );
        $campos['fac_graducao'] = array(
            'alias' => 'peg,fag,fg',
            'campo' => 'fg.nome',
            'nome' => 'Faculdade Graduacao',
            'function' => null
        );
        $campos['fac_pos_graducao'] = array(
            'alias' => 'pep,fap,fp',
            'campo' => 'fp.nome',
            'nome' => 'Faculdade Pos-Graduacao',
            'function' => null
        );
        $campos['fac_mestrado'] = array(
            'alias' => 'pem,fam,fm',
            'campo' => 'fm.nome',
            'nome' => 'Faculdade Mestrado',
            'function' => null
        );
        $campos['fac_doutorado'] = array(
            'alias' => 'ped,fad,fd',
            'campo' => 'fd.nome',
            'nome' => 'Faculdade Doutorado',
            'function' => null
        );
        $campos['cur_graducao'] = array(
            'alias' => 'peg,cag',
            'campo' => 'CASE WHEN cag.id = 1 THEN peg.curso ELSE cag.descricao END',
            'nome' => 'Curso de Graduacao',
            'function' => null
        );
        $campos['cur_pos_graducao'] = array(
            'alias' => 'pep,cap',
            'campo' => 'CASE WHEN cap.id = 1 THEN pep.curso ELSE cap.descricao END',
            'nome' => 'Curso de Pos-Graduacao',
            'function' => null
        );
        $campos['cur_mestrado'] = array(
            'alias' => 'pem,cam',
            'campo' => 'CASE WHEN cam.id = 1 THEN pem.curso ELSE cam.descricao END',
            'nome' => 'Curso de Mestrado',
            'function' => null
        );
        $campos['cur_doutorado'] = array(
            'alias' => 'ped,cad',
            'campo' => 'CASE WHEN cad.id = 1 THEN ped.curso ELSE cad.descricao END',
            'nome' => 'Curso de Doutorado',
            'function' => null
        );
        $campos['com_cep'] = array(
            'alias' => 'ec',
            'campo' => 'ec.cep',
            'nome' => 'COM - Cep',
            'function' => 'App_Utilidades::mask',
            'formato' => '99999-999'
        );
//        $campos['revista_vigentes'] = array(
//            'alias' => 'vpr',
//            'campo' => 'vpr.revista_vigentes',
//            'nome' => 'Revistas vigentes',
//            'function' => null
//        );
////        $campos['revista_anterior'] = array(
////            'alias' => 'vpr',
////            'campo' => 'vpr.revista_anterior',
////            'nome' => 'Revista anterior',
////            'function' => null
////        );
//        $campos['data_vigencia_associacao'] = array(
//            'alias' => 'i',
//            'campo' => 'i.data_vigencia_associacao',
//            'nome' => 'Vigencia na atividade selecionada',
//            'function' => 'App_Utilidades::mask',
//            'formato' => 'data'
//        );
//        $campos['data_vigencia_atual'] = array(
//            'alias' => 'vua',
//            'campo' => 'vua.data_inscricao',
//            'nome' => 'Vigencia Atual',
//            'function' => 'App_Utilidades::mask',
//            'formato' => 'data'
//        );
//        $campos['data_vigencia_anterior'] = array(
//            'alias' => 'vpa',
//            'campo' => 'vpa.data_inscricao',
//            'nome' => 'Vigencia Anterior',
//            'function' => 'App_Utilidades::mask',
//            'formato' => 'data'
//        );
        $campos['nome_atividade'] = array(
            'alias' => 'i,pp,aa,a',
            'campo' => 'a.descricao',
            'nome' => 'Nome Atividade',
            'function' => '',
            'formato' => ''
        );

        $campos['valor_atividade'] = array(
            'alias' => 'cr',
            'campo' => 'cr.valor',
            'nome' => 'Valor da Atividade',
            'function' => 'App_Utilidades::mask',
            'formato' => 'valor'
        );
        $campos['com_logradouro'] = array(
            'alias' => "ec,tlc",
            'campo' => "COALESCE(tlc.descricao,'') ||  ' ' || COALESCE(ec.logradouro,'')",
            'nome' => 'COM - Logradouro',
            'function' => null
        );
        $campos['com_pais'] = array(
            'alias' => 'pc',
            'campo' => 'pc.descricao',
            'nome' => 'COM - Pais',
            'function' => null
        );
        $campos['com_fone1'] = array(
            'alias' => 'ec',
            'campo' => 'ec.fone1',
            'nome' => 'Com. Tel 1',
            'function' => 'App_Utilidades::mask',
            'formato' => '(99) 9999-9999'
        );
        $campos['com_fone2'] = array(
            'alias' => 'ec',
            'campo' => 'ec.fone2',
            'nome' => 'COM - Tel. 2',
            'function' => 'App_Utilidades::mask',
            'formato' => '(99) 9999-9999'
        );
        $campos['com_uf'] = array(
            'alias' => 'ur,ec,uc',
            'campo' => 'uc.descricao',
            'nome' => 'COM - UF',
            'function' => null
        );
        $campos['com_cidade_uf'] = array(
            'alias' => 'ec,uc,mc',
            'campo' => "COALESCE(mc.descricao, '') || ' - ' || COALESCE(uc.codigo,'')",
            'nome' => 'COM - cidade-uf',
            'function' => null
        );
        $campos['com_celular'] = array(
            'alias' => 'ec',
            'campo' => 'ec.celular',
            'nome' => 'Com. Celular',
            'function' => 'App_Utilidades::mask',
            'formato' => '(99) 9999-9999'
        );
        $campos['com_bairro'] = array(
            'alias' => 'ec',
            'campo' => 'ec.bairro',
            'nome' => 'Com. Bairro',
            'function' => null
        );
        $campos['com_cidade'] = array(
            'alias' => 'ec,mc',
            'campo' => 'mc.descricao',
            'nome' => 'Com. Cidade',
            'function' => null
        );
        $campos['corr_bairro'] = array(
            'alias' => 'ecc',
            'campo' => 'ecc.bairro',
            'nome' => 'CORR - Bairro',
            'function' => null
        );
        $campos['corr_cep'] = array(
            'alias' => 'ecc',
            'campo' => 'ecc.cep',
            'nome' => 'CORR - Cep',
            'function' => 'App_Campos::cep',
        );
        $campos['corr_num'] = array(
            'alias' => 'ecc',
            'campo' => 'ecc.numero',
            'nome' => 'CORR - Num',
            'function' => null
        );
        $campos['corr_complemento'] = array(
            'alias' => 'ecc',
            'campo' => 'ecc.complemento',
            'nome' => 'CORR - Complemento',
            'function' => null
        );
        $campos['corr_cidade'] = array(
            'alias' => 'ecc,mcc',
            'campo' => 'mcc.descricao',
            'nome' => 'CORR - Cidade',
            'function' => null
        );
        $campos['corr_logradouro'] = array(
            'alias' => 'ecc,tlcc',
            'campo' => "COALESCE(tlcc.descricao, '') || ' ' || COALESCE(ecc.logradouro,'')",
            'nome' => 'CORR - Logradouro',
            'function' => null
        );
        $campos['corr_logradouro_num'] = array(
            'alias' => 'ecc,tlcc',
            'campo' => "COALESCE(tlcc.descricao, '') || ' ' || COALESCE(ecc.logradouro,'') || ', ' || COALESCE(ecc.numero,'')",
            'nome' => 'CORR - Logra., num.',
            'function' => null
        );
        $campos['corr_logradouro_num_comp'] = array(
            'alias' => 'ecc,tlcc',
            'campo' => "COALESCE(tlcc.descricao, '') || ' ' || COALESCE(ecc.logradouro,'') || ', ' || COALESCE(ecc.numero,'') ||  COALESCE(' - ' || ecc.complemento,'')",
            'nome' => 'CORR - Logra., num., comp.',
            'function' => null
        );
        $campos['corr_pais'] = array(
            'alias' => 'pcc',
            'campo' => 'pcc.descricao',
            'nome' => 'CORR - Pais',
            'function' => null
        );
        $campos['corr_fone1'] = array(
            'alias' => 'ecc',
            'campo' => 'ecc.fone1',
            'nome' => 'CORR - Tel 1',
            'function' => 'App_Utilidades::mask',
            'formato' => '(99) 9999-9999'
        );
        $campos['corr_uf'] = array(
            'alias' => 'ucc',
            'campo' => 'ucc.codigo',
            'nome' => 'CORR - UF',
            'function' => null
        );
        $campos['corr_cidade_uf'] = array(
            'alias' => 'ucc,ecc,mcc',
            'campo' => "COALESCE(mcc.descricao, '') || ' - ' || COALESCE(ucc.codigo,'')",
            'nome' => 'CORR. cidade-uf',
            'function' => null
        );

        $campos['corr_cep_cidade_uf'] = array(
            'alias' => 'ucc,ecc,mcc',
            'campo' => "COALESCE(substring(replace(ecc.cep,'.','') FROM 1 FOR 2) || '.' || substring(replace(ecc.cep,'-','') FROM 3 FOR 3) || '-' || substring(replace(ecc.cep,'-','') FROM 5 FOR 3), '') || ' ' || COALESCE(mcc.descricao, '') || ' - ' || COALESCE(ucc.codigo,'') ",
            'nome' => 'CORR - Cep cidade-uf',
            'function' => '',
        );

        $campos['categoria_profissional_id'] = array(
            'alias' => '',
            'campo' => 'va.categoria_profissional_id',
            'nome' => 'Cod. Categoria',
            'function' => null
        );
        $campos['regional_id'] = array(
            'alias' => '',
            'campo' => 'va.regional_id',
            'nome' => 'Cod. Regional',
            'function' => null
        );
        $campos['id'] = array(
            'alias' => '',
            'campo' => 'va.id',
            'nome' => 'Codigo Ass.',
            'function' => null
        );
        $campos['data_cadastro'] = array(
            'alias' => '',
            'campo' => 'va.data_cadastro',
            'nome' => 'Data de cadastro',
            'function' => 'App_Utilidades::mask',
            'formato' => 'data'
        );
        $campos['data_nascimento'] = array(
            'alias' => '',
            'campo' => 'va.data_nascimento',
            'nome' => 'Data de nascimento',
            'function' => 'App_Utilidades::mask',
            'formato' => 'data'
        );
        $campos['data_atualizacao'] = array(
            'alias' => 'ec',
            'campo' => 'ec.data_atualizacao',
            'nome' => 'Dt. Atual. End.',
            'function' => 'App_Utilidades::mask',
            'formato' => 'data'
        );
        $campos['data_baixa'] = array(
            'alias' => 'bcr,cr,i',
            'campo' => 'bcr.data_baixa',
            'nome' => 'Dt. Pagamento',
            'function' => 'App_Date::formatar',
            'formato' => 'd/m/Y'
        );
        $campos['data_falecimento'] = array(
            'alias' => '',
            'campo' => 'va.data_falecimento',
            'nome' => 'Dt. de Falecimento',
            'function' => 'App_Utilidades::mask',
            'formato' => 'data'
        );
        $campos['data_cancelamento'] = array(
            'alias' => '',
            'campo' => 'CASE WHEN va.status=3 THEN va.data_status ELSE NULL END',
            'nome' => 'Dt. de Cancelamento',
            'function' => null
        );
        $campos['end_com_completo'] = array(
            'alias' => 'ec,tlc',
            'campo' => "COALESCE(tlc.descricao,'') ||  ' ' || COALESCE(ec.logradouro,'') || ', ' || COALESCE(ec.numero,'') || ' ' || COALESCE(ec.complemento,'') || ' Bairro: ' || COALESCE(ec.bairro,'') || ' CEP: ' || COALESCE(ec.cep,'')",
            'nome' => 'End Com. Completo',
            'function' => null
        );
        $campos['end_corr_completo'] = array(
            'alias' => 'ecc,tlcc',
            'campo' => "COALESCE(ecc.logradouro,'') || ', ' || COALESCE(ecc.numero,'') || ' ' || COALESCE(ecc.complemento,'') || ' Bairro: ' || COALESCE(ecc.bairro,'') || ' CEP: ' || COALESCE(ecc.cep,'')",
            'nome' => 'End Corr. Completo',
            'function' => null
        );
        $campos['end_res_completo'] = array(
            'alias' => 'er,tlr',
            'campo' => "COALESCE(tlr.descricao,'') ||  ' ' || COALESCE(er.logradouro,'') || ', ' || COALESCE(er.numero,'') || ' ' || COALESCE(er.complemento,'') || ' Bairro: ' || COALESCE(er.bairro,'') || ' CEP: ' || COALESCE(er.cep,'')",
            'nome' => 'End Res. Completo',
            'function' => null
        );
        $campos['entidade'] = array(
            'alias' => 'e',
            'campo' => 'e.nome',
            'nome' => 'Entidade Parceira',
            'function' => null
        );
        $campos['especialidade'] = array(
            'alias' => 'pe,esp',
            'campo' => 'esp.descricao',
            'nome' => 'Especialidade',
            'function' => null
        );
        $campos['matricula'] = array(
            'alias' => '',
            'campo' => 'va.matricula',
            'nome' => 'Matricula',
            'function' => null
        );
        $campos['rg'] = array(
            'alias' => '',
            'campo' => 'va.rg',
            'nome' => 'RG',
            'function' => null
        );
        $campos['regional'] = array(
            'alias' => 'r',
            'campo' => 'r.descricao',
            'nome' => 'Regional',
            'function' => null
        );
        $campos['res_bairro'] = array(
            'alias' => 'er',
            'campo' => 'er.bairro',
            'nome' => 'Res. Bairro',
            'function' => null
        );
        $campos['res_celular'] = array(
            'alias' => 'er',
            'campo' => 'er.celular',
            'nome' => 'Res. Celular',
            'function' => null
        );
        $campos['res_cep'] = array(
            'alias' => 'er',
            'campo' => 'er.cep',
            'nome' => 'Res. Cep',
            'function' => 'App_Utilidades::mask',
            'formato' => '99999-999'
        );
        $campos['res_cidade'] = array(
            'alias' => 'er,mr',
            'campo' => 'mr.descricao',
            'nome' => 'Res. Cidade',
            'function' => null
        );
        $campos['res_logradouro'] = array(
            'alias' => 'er',
            'campo' => 'er.logradouro',
            'nome' => 'Res. Logradouro',
            'function' => null
        );
        $campos['res_pais'] = array(
            'alias' => 'pr',
            'campo' => 'pr.descricao',
            'nome' => 'Res. Pais',
            'function' => null
        );
        $campos['res_fone1'] = array(
            'alias' => 'er',
            'campo' => 'er.fone1',
            'nome' => 'Res. Tel 1',
            'function' => null
        );
        $campos['res_fone2'] = array(
            'alias' => 'er',
            'campo' => 'er.fone2',
            'nome' => 'Res. Tel 2',
            'function' => null
        );
        $campos['res_uf'] = array(
            'alias' => 'er,ur',
            'campo' => 'ur.descricao',
            'nome' => 'Res. UF',
            'function' => null
        );
        $campos['res_cidade_uf'] = array(
            'alias' => 'er,ur,mr',
            'campo' => "COALESCE(mr.descricao, '') || '-' || COALESCE(ur.codigo,'')",
            'nome' => 'Res - cidade-uf',
            'function' => null
        );
//        $campos['id_site_sae'] = array(
//            'alias' => 'ctv5',
//            'campo' => 'ctv5.valor_tipo_4',
//            'nome' => 'ID SITE SAE',
//            'function' => null
//        );
//        $campos['senha_site_sae'] = array(
//            'alias' => 'ctv7',
//            'campo' => 'ctv7.valor_tipo_4',
//            'nome' => 'Senha SITE SAE',
//            'function' => null
//        );

        $campos['cargo'] = array(
            'alias' => 'pv,cargo',
            'campo' => 'cargo.descricao',
            'nome' => 'Cargo',
            'function' => null
        );
// financeiro

        $campos['situacao_financeira'] = array(
            'alias' => '',
            'campo' => 'pessoa_assoc_financ_ent_f(va.pessoa_id, va.associacao_id)',
            'nome' => 'Situação financeira',
            'function' => 'ContaReceberTable::situacaoFinanceira',
            'formato' => null
        );

//ABCDI
        $campos['abcdi_modalidades'] = array(
            'alias' => 'cli,clm,clmod',
            'campo' => 'clmod.descricao',
            'nome' => 'Abcdi Modalidades',
            'function' => null
        );
        $campos['abcdi_equipamentos'] = array(
            'alias' => 'cli,cle,clteq',
            'campo' => 'clteq.descricao',
            'nome' => 'Abcdi Equipamentos',
            'function' => null
        );
        $campos['abcdi_situacao'] = array(
            'alias' => 'cli',
            'campo' => 'cli.situacao',
            'nome' => 'Abcdi Situacao',
            'function' => 'App_Status::situacaoSimNao'
        );

// campos dos filtros relacionados à SOBRICE ---------------------------
        $campos['sobrice_socio'] = array(
            'alias' => 'viepo',
            'campo' => 'viepo.sobrice_socio',
            'nome' => 'Sobrice sócio',
            'function' => 'App_Status::situacaoSimNao'
        );

        $campos['sobrice_categoria'] = array(
            'alias' => 'viepo',
            'campo' => 'viepo.f_sobrice_categoria',
            'nome' => 'Sobrice categoria',
            'function' => null
        );

// ---------------------------------------------------------------------

        return $campos;
    }

    public static function camposPatrocinios() {
        $campos = array();

// Empresa
        $campos['p0_id'] = array(
            'alias' => 'cp0,p0',
            'campo' => 'p0.id',
            'nome' => 'Empresa - Id',
            'function' => null
        );
        $campos['p0_razao_social'] = array(
            'alias' => 'cp0,p0',
            'campo' => 'p0.razao_social',
            'nome' => 'Empresa - Razao Social',
            'function' => null
        );
        $campos['p0_nome_empresa'] = array(
            'alias' => 'cp0,p0',
            'campo' => 'p0.nome',
            'nome' => 'Empresa - Nome da empresa',
            'function' => null
        );

// Contato da empresa
        $campos['p3_nome'] = array(
            'alias' => 'cp3,p3',
            'campo' => 'p3.nome',
            'nome' => 'Contato  - Nome do contato',
            'function' => null
        );
        $campos['p3_email'] = array(
            'alias' => 'cp3,p3',
            'campo' => 'p3.email',
            'nome' => 'Contato  - E-mail',
            'function' => null
        );
        $campos['cp3_cargo_captacao'] = array(
            'alias' => 'cp3',
            'campo' => 'cp3.cargo',
            'nome' => 'Contato  - Cargo',
            'function' => null
        );
        $campos['cp3_departamento_captacao'] = array(
            'alias' => 'cp3',
            'campo' => 'cp3.departamento',
            'nome' => 'Contato - Departamento',
            'function' => null
        );
        $campos['p3_cpf'] = array(
            'alias' => 'cp3,p3',
            'campo' => 'p3.cpf',
            'nome' => 'Contato - CPF',
            'function' => 'App_Campos::cpf'
        );
        $campos['p3_rg'] = array(
            'alias' => 'cp3,p3',
            'campo' => 'p3.rg',
            'nome' => 'Contato - RG',
            'function' => null
        );
        $campos['p3_sexo'] = array(
            'alias' => 'cp3,p3',
            'campo' => 'p3.sexo',
            'nome' => 'Contato - Sexo',
            'function' => 'App_Campos::sexo'
        );

// Endereco de contato
        $campos['e3_pais'] = array(
            'alias' => 'cp3,p3,e3,e3_p',
            'campo' => 'e3_p.descricao',
            'nome' => 'End. Contato - Pais',
            'function' => null
        );
        $campos['e3_cep'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => 'e3.cep',
            'nome' => 'End. Contato - CEP',
            'function' => 'App_Utilidades::mask',
            'formato' => '99999-999'
        );
        $campos['e3_logradouro'] = array(
            'alias' => 'cp3,p3,e3,e3_tl',
            'campo' => "COALESCE(e3_tl.descricao, '') || ' ' || COALESCE(e3.logradouro, '')",
            'nome' => 'End. Contato - Logradouro'
        );
        $campos['e3_numero'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => "e3.numero",
            'nome' => 'End. Contato - Numero',
            'function' => null
        );
        $campos['e3_complemento'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => "e3.complemento",
            'nome' => 'End. Contato - Complemento',
            'function' => null
        );
        $campos['e3_bairro'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => "e3.bairro",
            'nome' => 'End. Contato - Bairro',
            'function' => null
        );
        $campos['e3_uf'] = array(
            'alias' => 'cp3,p3,e3,e3_m',
            'campo' => "e3_m.uf",
            'nome' => 'End. Contato - UF',
            'function' => null
        );
        $campos['e3_minicipio'] = array(
            'alias' => 'cp3,p3,e3,e3_m',
            'campo' => "e3_m.descricao",
            'nome' => 'End. Contato - Municipio',
            'function' => null
        );
        $campos['e3_telefone'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => "COALESCE(e3.ddi1, '') || ' ' || COALESCE(e3.fone1, '') || ' ' || COALESCE(e3.ramal1, '')",
            'nome' => 'End. Contato - Telefone',
            'function' => null
        );
        $campos['e3_fax'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => "COALESCE(e3.ddi_fax, '') || ' ' || COALESCE(e3.fax, '') || ' ' || COALESCE(e3.ramal_fax, '')",
            'nome' => 'End. Contato - Fax',
            'function' => null
        );
        $campos['e3_celular'] = array(
            'alias' => 'cp3,p3,e3',
            'campo' => "COALESCE(e3.ddi_celular, '') || ' ' || COALESCE(e3.celular, '')",
            'nome' => 'End. Contato - Celular',
            'function' => null
        );

// Contrato
        $campos['c_id'] = array(
            'alias' => null,
            'campo' => "c.id",
            'nome' => 'Contrato - ID do contrato',
            'function' => null
        );
        $campos['c_objeto_negociacao'] = array(
            'alias' => null,
            'campo' => "c.objeto_negociacao",
            'nome' => 'Contrato - Beneficios contemplados',
            'function' => null
        );
        $campos['c_valor_entrada'] = array(
            'alias' => null,
            'campo' => "c.valor_entrada",
            'nome' => 'Contrato - Valor contrato',
            'function' => 'App_Moeda::formatar'
        );
        $campos['c_num_parcelas'] = array(
            'alias' => null,
            'campo' => "c.num_parcelas",
            'nome' => 'Contrato - Quant. Parcelas',
            'function' => null
        );
        $campos['c_codigo'] = array(
            'alias' => null,
            'campo' => "c.codigo",
            'nome' => 'Contrato - Codigo do contrato',
            'function' => null
        );
        $campos['c_situacao'] = array(
            'alias' => null,
            'campo' => "c.situacao",
            'nome' => 'Contrato - Situacao',
            'function' => null
        );
        $campos['c_forma_pagamento'] = array(
            'alias' => null,
            'campo' => "c.forma_pagamento",
            'nome' => 'Contrato - Forma de pagamento',
            'function' => null
        );
        $campos['c_data_envio_manual'] = array(
            'alias' => null,
            'campo' => "c.data_envio_manual",
            'nome' => 'Contrato - Data envio manual',
            'function' => 'App_Utilidades::mask',
            'formato' => 'data'
        );
        $campos['p3_foto'] = array(
            'alias' => 'cp3,p3',
            'campo' => "p3.foto",
            'nome' => 'Contrato - Logo',
            'function' => null
        );
        $campos['c_data_recebimento_contrato'] = array(
            'alias' => null,
            'campo' => "c.data_recebimento_contrato",
            'nome' => 'Contrato - Data recebimento contrato',
            'function' => null
        );
        $campos['c_data_envio_contrato'] = array(
            'alias' => null,
            'campo' => "c.data_envio_contrato",
            'nome' => 'Contrato - Data envio contrato',
            'function' => 'App_Utilidades::mask',
            'formato' => 'data'
        );

// Empresa de cobrança
        $campos['p1_razao_social'] = array(
            'alias' => 'cp1,p1',
            'campo' => "p1.razao_social",
            'nome' => 'Empresa de Cobranca - Razao Social',
            'function' => null
        );
        $campos['p1_cnpj'] = array(
            'alias' => 'cp1,p1',
            'campo' => "p1.cnpj",
            'nome' => 'Empresa de Cobranca - CNPJ',
            'function' => 'App_Campos::cnpj'
        );
        $campos['p1_inscricao_estadual'] = array(
            'alias' => 'cp1,p1',
            'campo' => "p1.inscricao_estadual",
            'nome' => 'Empresa de Cobranca - Inscricao Estadual',
            'function' => null
        );
        $campos['e1_pais'] = array(
            'alias' => 'cp1,p1,e1,e1_p',
            'campo' => "e1_p.descricao",
            'nome' => 'Empresa de Cobranca - Pais',
            'function' => null
        );
        $campos['e1_cep'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "e1.cep",
            'nome' => 'Empresa de Cobranca - CEP',
            'function' => 'App_Utilidades::mask',
            'formato' => '99999-999'
        );
        $campos['e1_logradouro'] = array(
            'alias' => 'cp1,p1,e1,e1_tl',
            'campo' => "COALESCE(e1_tl.descricao, '') || ' ' || COALESCE(e1.logradouro, '')",
            'nome' => 'Empresa de Cobranca - Logradouro',
            'function' => null
        );
        $campos['e1_numero'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "e1.numero",
            'nome' => 'Empresa de Cobranca - Numero',
            'function' => null
        );
        $campos['e1_complemento'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "e1.complemento",
            'nome' => 'Empresa de Cobranca - Complemento',
            'function' => null
        );
        $campos['e1_bairro'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "e1.bairro",
            'nome' => 'Empresa de Cobranca - Bairro',
            'function' => null
        );
        $campos['e1_uf'] = array(
            'alias' => 'cp1,p1,e1,e1_m',
            'campo' => "e1_m.uf",
            'nome' => 'Empresa de Cobranca - UF',
            'function' => null
        );
        $campos['e1_municipio'] = array(
            'alias' => 'cp1,p1,e1,e1_m',
            'campo' => "e1_m.descricao",
            'nome' => 'Empresa de Cobranca - Municipio',
            'function' => null
        );
        $campos['e1_telefone'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "COALESCE(e1.ddi1, '') || ' ' || COALESCE(e1.fone1, '') || ' ' || COALESCE(e1.ramal1, '')",
            'nome' => 'End. Contato - Telefone',
            'function' => null
        );
        $campos['e1_fax'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "COALESCE(e1.ddi_fax, '') || ' ' || COALESCE(e1.fax, '') || ' ' || COALESCE(e1.ramal_fax, '')",
            'nome' => 'End. Contato - Fax',
            'function' => null
        );
        $campos['e1_celular'] = array(
            'alias' => 'cp1,p1,e1',
            'campo' => "COALESCE(e1.ddi_celular, '') || ' ' || COALESCE(e1.celular, '')",
            'nome' => 'End. Contato - Celular',
            'function' => null
        );

        $campos['area_objeto_negociado'] = array(
            'alias' => '',
            'campo' => "area_objeto_negociado",
            'nome' => 'Metragem total',
            'function' => null
        );

// Contato de cobranca
        $arrayContatos = array(
            '2' => 'Contato de cobranca',
            '4' => 'Responsavel pagamento',
            '5' => 'Representante legal',
            '6' => 'Representante legal 2'
        );

        foreach ($arrayContatos as $key => $c) {
            $campos['p' . $key . '_nome'] = array(
                'alias' => "cp{$key},p{$key}",
                'campo' => "p{$key}.nome",
                'nome' => $c . ' - Nome',
                'function' => null
            );
            $campos['p' . $key . '_email'] = array(
                'alias' => "cp{$key},p{$key}",
                'campo' => "p" . $key . ".email",
                'nome' => $c . ' - E-mail',
                'function' => null
            );
            $campos['cp' . $key . '_cargo'] = array(
                'alias' => 'cp' . $key,
                'campo' => "cp" . $key . ".cargo",
                'nome' => $c . ' - Cargo',
                'function' => null
            );
            $campos['cp' . $key . '_departamento'] = array(
                'alias' => 'cp' . $key,
                'campo' => "cp" . $key . ".departamento",
                'nome' => $c . ' - Departamento',
                'function' => null
            );
            $campos['p' . $key . '_cpf'] = array(
                'alias' => "cp{$key},p{$key}",
                'campo' => "p" . $key . ".cpf",
                'nome' => $c . ' - CPF',
                'function' => 'App_Campos::cpf'
            );
            $campos['p' . $key . '_rg'] = array(
                'alias' => "cp{$key},p{$key}",
                'campo' => "p" . $key . ".rg",
                'nome' => $c . ' - RG',
                'function' => 'App_Campos::cpf'
            );
            $campos['p' . $key . '_sexo'] = array(
                'alias' => "cp{$key},p{$key}",
                'campo' => "p" . $key . ".sexo",
                'nome' => $c . ' - Sexo',
                'function' => 'App_Campos::sexo'
            );
        }


        return $campos;
    }

    public static function camposTrabalhos() {
        $campos = array();


        $campos['trabalho_id'] = array(
            'alias' => '',
            'campo' => 't.id',
            'nome' => 'Codigo Trabalho',
            'function' => null
        );

        $campos['instituicoes'] = array(
            'alias' => '',
            'campo' => "array_to_string(array(SELECT ta1.instituicao::TEXT||'<br/>' FROM trabalhos_autores ta1 INNER JOIN pessoas r ON r.id = ta1.pessoa_id where ta1.trabalho_id = t.id),' ' )",
            'nome' => 'Instituicões',
            'function' => null,
            'exibir' => false
        );

        $campos['autores'] = array(
            'alias' => '',
            'campo' => "array_to_string(array(SELECT r.nome::TEXT||'<br/>' FROM pessoas r INNER JOIN trabalhos_autores ta1 ON r.id = ta1.pessoa_id where ta1.trabalho_id = t.id),' ' )",
            'nome' => 'Autores',
            'function' => null,
            'exibir' => false
        );

        $campos['titulo'] = array(
            'alias' => '',
            'campo' => 't.titulo',
            'nome' => 'Titulo',
            'function' => null
        );

        $campos['keyword'] = array(
            'alias' => '',
            'campo' => 't.keyword',
            'nome' => 'Palavra Chave',
            'function' => null
        );

        $campos['keyword_lng'] = array(
            'alias' => '',
            'campo' => 't.keyword_lng',
            'nome' => 'Palavra Chave em ingles',
            'function' => null
        );

        $campos['titulo_lng'] = array(
            'alias' => '',
            'campo' => 't.titulo_lng',
            'nome' => 'Titulo em ingles',
            'function' => null
        );

        $campos['nome_autor'] = array(
            'alias' => 'pa',
            'campo' => 'pa.nome',
            'nome' => 'Nome do autor',
            'function' => null
        );

        $campos['email_autor'] = array(
            'alias' => 'pa',
            'campo' => 'pa.email',
            'nome' => 'E-mail do autor',
            'function' => null
        );

        $campos['empresa'] = array(
            'alias' => 'pcc,emp',
            'campo' => 'emp.nome',
            'nome' => 'Empresa',
            'function' => null
        );

        $campos['departamento'] = array(
            'alias' => 'pcc',
            'campo' => 'pcc.departamento',
            'nome' => 'Departamento',
            'function' => null
        );

        $campos['tema'] = array(
            'alias' => 'ct',
            'campo' => 'ct.descricao',
            'nome' => 'Tema',
            'function' => null
        );

        if (CLIENTE == 'sbgg') {
            $campos['sub_tema'] = array(
                'alias' => 'cst',
                'campo' => 'cst.descricao',
                'nome' => 'Sub Tema',
                'function' => null
            );
        }

        $campos['status_fase'] = array(
            'alias' => 'th',
            'campo' => 'th.status',
            'nome' => 'Status na fase',
            'function' => 'App_Status::trabalhos'
        );

        $campos['ativo'] = array(
            'alias' => 'th',
            'campo' => 'th.exibir',
            'nome' => 'Ativo?',
            'function' => 'App_Campos::simNao'
        );

        $campos['resumo'] = array(
            'alias' => '',
            'campo' => 't.resumo',
            'nome' => 'Resumo',
            'function' => 'App_Campos::resumoInglesExcel'
        );

        $campos['resumo_ingles'] = array(
            'alias' => '',
            'campo' => 't.resumo_lng',
            'nome' => 'Resumo em ingles',
            'function' => 'App_Campos::resumoInglesExcel'
        );

        $campos['nome_arquivo'] = array(
            'alias' => 'th',
            'campo' => 'th.nome_arquivo',
            'nome' => 'Nome do arquivo',
            'function' => 'App_Campos::nomeArquivoTrabalho'
        );

        return $campos;
    }

    /**
     * Converte valor 
     * @param double $valor
     * @return double
     */
    public static function converteValorString2Double($valor) {
        $valor = str_replace(" ", "", $valor);
        $valor = str_replace("R$", "", $valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", ".", $valor);
        return $valor;
    }

    /**
     * Converte de cm para milimetro
     * @param double $cm
     * @return double
     */
    public static function convertCmForMm($cm) {
        return $cm * 10;
    }

    /* Quando o usuario deixa o campo vazio nao atualiza o campo, por conta de uma regra que e chamada
     * dentro da função save, por isso foi criado essa regra
     * obs.:quando tiramos essa regra deu error na geração do boleto.Deixo claro que devemos corrigir esse erro
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

    public static function getNavegador() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'IE';
        } elseif (preg_match('|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Opera';
        } elseif (preg_match('|Firefox/([0-9\.]+)|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Firefox';
        } elseif (preg_match('|Chrome/([0-9\.]+)|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Chrome';
        } elseif (preg_match('|Safari/([0-9\.]+)|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Safari';
        } else {
            $browser_version = 0;
            $browser = 'other';
        }
        return array('navegador' => $browser, 'versao' => $browser_version);
    }

    public static function getComprovante($pessoaCentroCustoId, $nomeArquivo) {
        try {
            if (!is_numeric($pessoaCentroCustoId)) {
                throw new Exception("Pessoa centro de custo invalido!");
            }
            return PUBLIC_PATH . "arquivos/clientes/" . CLIENTE . "/comprovante/{$nomeArquivo}";
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function tratamentoConfiguracao($codigos, $indice, $indice2 = '') {
        $retorno = array();
        $codigos = explode("\n", $codigos['valor_referencia']);
        foreach ($codigos as $e) {
            $temp = explode('|', $e);
            if (!empty($temp[0]) && !empty($temp[1])) {
                if (!empty($indice2)) {
                    $retorno[$temp[0]][$temp[1]] = $temp[2];
                } else {
                    $retorno[$temp[0]] = $temp[1];
                }
            }
        }

        if (!empty($indice2)) {
            return isset($retorno[$indice][$indice2]) ? $retorno[$indice][$indice2] : '';
        }

        return ($retorno[$indice]) ? $retorno[$indice] : '';
    }

    public static function getPagina($caminho, $arquivo) {
        $caminhos = array();
        $caminhos['pagina_associado'] = PUBLIC_PATH . "arquivos/clientes/" . CLIENTE . "/pagina_associado/";
        if (isset($caminhos[$caminho]) && file_exists($caminhos[$caminho] . $arquivo)) {
            return file_get_contents($caminhos[$caminho] . $arquivo);
        }
    }

    /**
     * Retorna situacao financeira da pessoa naquele entidade
     * @param array $configuracao configuracao para saber se vai verificar situacao financeira do socio
     * @param array $params array contendo a pessoa_id e entidade_id da pessoa a ser verificada
     * @return mixed se a pessoa for nao socio vai sair null, se nao vai sair string 
     */
    public static function getSituacaoFinanceira($configuracao, array $params) {

        $situacaoEntidade = null;

        if (isset($params['pessoa_id']) && count($configuracao) && $configuracao['valor_referencia'] == '1') {
            $associacaoTable = new Associacao;
            $associado = $associacaoTable->findByPessoaIdAndEntidadeId($params['pessoa_id']);
            if (count($associado)) {
                $status = $associacaoTable->status($params['pessoa_id'], $associado['id']);
                $primeiraAssociacao = $associacaoTable->primeiraAssociacao($params['pessoa_id'], $params['entidade_id']);
//            $situacaoEntidade = $status['pessoa_assoc_financ_ent_f'];//Temporário
                if (($status['pessoa_assoc_financ_ent_f'] == 'Q' || $status['pessoa_assoc_financ_ent_f'] == 'E') && count($primeiraAssociacao)) {
                    $situacaoEntidade = Associacao::EM_DIAS;
                } elseif (count($primeiraAssociacao) && $status['pessoa_assoc_financ_ent_f'] != 'Q' && $status['pessoa_assoc_financ_ent_f'] != 'E') {
                    $situacaoEntidade = Associacao::EM_DEBITO;
                }
            }
            if (is_null($situacaoEntidade) && is_numeric($params['entidadeIdParceira'])) {
                $situacaoEntidade = Associacao::PARCEIRO;
            }
        }

        return $situacaoEntidade;
    }

    /**
     * Transforma uma string em array, a cada virgula e uma nova linha
     * @param string $string string a ser convertida 
     * @param string $explode chave que divide as informacoes
     * @return array
     */
    public static function stringToArray($string, $explode = '|') {
        $retorno = array();
        $string = explode(',', $string);

        foreach ($string as $s) {
            $retorno[] = preg_replace('/[\s]/i', '', explode($explode, trim($s)));
        }
        return $retorno;
    }

    /**
     * Esta função pesquisa se o usuário está inserido no centro de custo
     * e na categoria profissional
     * 
     * @param int $centroCusto
     * @param int $categoriaProfissional
     * @param Pessoa $pessoa
     * @since 2014
     * @version 1.0
     * @author Alcides Bezerra
     */
    public static function inserirCentroCusto($centroCusto, $categoriaProfissional, $pessoa) {

        $pessoaCentroCustoTable = new PessoaCentroCusto();
        $pessoaTable = new Pessoa();
        $categoriaCentroCustoTable = new CategoriaCentroCusto();
        $enderecoTable = new Endereco();
        $configCliente = Zend_Registry::get('configCliente');
        $entidadeId = $configCliente->dados->entidade->codigo;

        $retorno = array();

        //verifica a existência da categoria para o centro de custo
        $rsccc = $categoriaCentroCustoTable->fetchRow("centro_custo_id = {$centroCusto} and categoria_profissional_id = {$categoriaProfissional}");

        //Caso a categoria exista, ele tenta inserir o usuário no centro de custo
        if ($rsccc) {

            //Verifica se a pessoa está no centro de custo, antes de inserí-lo
            $pessoaCC = $pessoaCentroCustoTable->findByEmailOrCpfAndCentroCustoId($pessoa['cpf'], $centroCusto);

            //Caso não esteja noc entro de custo, iniciará a rotina de insersão
            if (!$pessoaCC) {
                $enderecoPrincipal = $enderecoTable->getEnderecoPrincipal($pessoa['id'], $entidadeId);
                $dadosPessoaCentroCusto = array();
                $dadosPessoaCentroCusto['pessoa_id'] = $pessoa['id'];
                $dadosPessoaCentroCusto['centro_custo_id'] = $centroCusto;
                $dadosPessoaCentroCusto['endereco_id'] = $enderecoPrincipal->getId();
                $dadosPessoaCentroCusto['categoria_centro_custo_id'] = $rsccc['id'];
                $dadosPessoaCentroCusto['categoria_profissional_id'] = $categoriaProfissional;
                $dadosPessoaCentroCusto['categoria_centro_custo_princ'] = 'S';
                $dadosPessoaCentroCusto['nome_cracha'] = $pessoa['nome'];

                $retorno[] = $pessoaCentroCustoTable->save($dadosPessoaCentroCusto);
            }
        }

        return $retorno;
    }

    public static function getValorAtividade(array $dados, $situacaoEntidade) {
        switch ($situacaoEntidade):
            case Associacao::EM_DEBITO:
                $valorRealAtividade = $dados['valor_socio_n_qd'];
                $valorAtividade = $dados['valor_socio_n_qd'];
                $valorMatricula = $dados['valor_mat_socio_n_qd'];
                break;
            case Associacao::REMIDO:
            case Associacao::EM_DIAS:
                $valorRealAtividade = $dados['valor_socio_qd'];
                $valorAtividade = $dados['valor_socio_qd'];
                $valorMatricula = $dados['valor_mat_socio_qd'];
                break;
            case Associacao::PARCEIRO:
                $valorRealAtividade = $dados['valor_socio_parceiro'];
                $valorAtividade = $dados['valor_socio_parceiro'];
                $valorMatricula = $dados['valor_mat_socio_parceiro'];
                break;
            default:
                $valorRealAtividade = $dados['valor_nao_socio'];
                $valorAtividade = $dados['valor_nao_socio'];
                $valorMatricula = $dados['valor_mat_nao_socio'];
                break;
        endswitch;

        return array('valorMatricula' => $valorMatricula, 'valorRealAtividade' => $valorRealAtividade, 'valorAtividade' => $valorAtividade,);
    }

    /**
     * Retorna um array com cada indice com um keyword
     * @param string $keywords keywords do trabalho
     * @return array
     */
    public static function keywordsTrabalho($keywords) {
        return !empty($keywords) ? explode('|', $keywords) : '';
    }

    public static function getConteudoEmail(array $params) {
        $conteudoEmails = new ConteudoEmails();
        $idiomas = new Idiomas();
        $dados = array();
        $retorno = array();
        $rsIdioma = $idiomas->fetchRow("UPPER(abreviacao) = UPPER('{$params['idioma']}')");
        $dados['tes.id'] = $params['tes.id'];
        $dados['idm.id'] = $rsIdioma['id'];

        //carregando conteudo
        $rsConteudoEmails = $conteudoEmails->findConteudoEmail($dados);

        foreach ($rsConteudoEmails as $r) {

            if (isset($params['centro_custo_id']) && ($r['centro_custo_id'] == $params['centro_custo_id'])) {
                $retorno[$params['centro_custo_id']] = $r;
            } elseif (isset($params['atividade_id']) && ($r['atividade_id'] == $params['atividade_id'])) {
                $retorno[$params['atividade_id']] = $r;
            } elseif (isset($params['agenda_atividade_id']) && ($r['agenda_atividade_id'] == $params['agenda_atividade_id'])) {
                $retorno[$params['agenda_atividade_id']] = $r;
            } elseif (isset($params['categoria_centro_custo_id']) && ($r['categoria_centro_custo_id'] == $params['categoria_centro_custo_id'])) {
                $retorno[$params['categoria_centro_custo_id']] = $r;
            }

            if (!count($retorno)) {
                if (empty($r['centro_custo_id']) && $r['atividade_id'] && $r['agenda_atividade_id'] && $r['categoria_centro_custo_id']) {
                    $retorno[] = $r;
                }
            }
        }

        return $retorno;
    }

    /**
     * Retorna os campos disponiveis do relatorio analitico
     * @return array campos disponiveis
     */
    public static function camposRelatorioAnalitico() {

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

        $campos['p0_id'] = array(
            'alias' => 'cp0,p0',
            'campo' => 'p0.id',
            'nome' => $translate->_('Atividades'),
            'function' => null
        );

        return $campos;
    }

}
