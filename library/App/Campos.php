<?php

class App_Campos {

    public static function resumoInglesExcel($text) {

        return strip_tags($text);
    }

    public static function nomeArquivoTrabalho($arq) {

        return $arq;
    }

    public static function cep($valor) {

        $maskared = '';
        $mask = '99.999-999';

        // por algum motivo na etiqueta está vindo com mascara alguns campos,
        //deve estar no banco assim, assim que tiver um tempinho concertar, e 
        //retirar esse filtro
        $filter = new Zend_Filter_Digits();
        $valor = $filter->filter($valor);
        //---------------------------------------------------------------------
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '9') {
                if (isset($valor[$k]))
                    $maskared .= $valor[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

    public static function cnpj($valor) {

        $maskared = '';
        $mask = '99.999.999/9999-99';

        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '9') {
                if (isset($valor[$k]))
                    $maskared .= $valor[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

    public static function cpf($valor) {

        $maskared = '';
        $mask = '999.999.999-99';

        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '9') {
                if (isset($valor[$k]))
                    $maskared .= $valor[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

    public static function tipoPessoaContato($valor) {

        $array = array();
        $array[0] = 'Favorecido';
        $array[1] = 'Cobrado';
        $array[2] = 'Contato de cobranca';
        $array[3] = 'Contato principal';
        $array[4] = 'Contato pagamento';
        $array[5] = 'Representante legal 1';
        $array[6] = 'Representante legal 2';

        if (key_exists($valor, $array))
            return $array[$valor];
        else
            return null;
    }

    public static function simNao($valor) {

        if ($valor == 'S')
            return 'Sim';
        else if ($valor == 'N')
            return 'Não';
        else
            return null;
    }

    public static function telefone($ddi, $numero, $ramal = null) {

        return "{$ddi} {$numero} " . ($ramal != null ? " R. {$ramal}" : "");
    }

    public static function origemConta($valor) {

        $conta = array();
        $conta[0] = 'Nao definido';
        $conta[3] = 'Boleto';
        $conta[5] = 'Cartao';
        $conta[10] = 'Transferencia';
        $conta[11] = 'Empenho';

        if (key_exists($valor, $conta))
            return $conta[$valor];
        else
            return null;
    }

    public static function data($data, $formato = 'd/m/Y') {

        if (isset($data) && !empty($data))
            $formatar = date($formato, strtotime($data));
        else
            $formatar = null;

        return $formatar;
    }

    public static function tipoDesconto($valor) {
        $valor = trim($valor);

        if ($valor == 'P')
            return 'Porcentagem (%)';
        else
            return 'Valor (R$)';
    }

    public static function necessidadesEspeciais($valor) {

        if ($valor == 'S')
            return 'Sim';
        else
            return 'Nao';
    }

    public static function instituicaoEnsino($id, $nome) {

        if (isset($nome) && !empty($nome))
            return $nome;

        $pessoa = new Pessoa();
        $campo = $pessoa->findById($id);

        return $campo['nome'];
    }

    public static function comoSoubeEvento($valor) {
        $valor = explode(',', $valor);

        if ($valor[0] == "99")
            return $valor[1];

        $configuracoes = new Configuracoes();
        $campos = $configuracoes->getByCodigo(146);
        $opcoesNiveis = explode("\n", $campos['valor_referencia']);

        foreach ($opcoesNiveis as $opc) {
            $opcao = explode('|', $opc);
            if ($opcao[0] == $valor[0])
                return $opcao[1];
        }
    }

    public static function nivelHierarquico($valor, $profPredominante) {
        $valor = explode(',', $valor);

        if ($valor[0] == "99")
            return $valor[1];

        if ($profPredominante == 1)
            $codigoTipo = 125;
        else
            $codigoTipo = 126;

        $configuracoes = new Configuracoes();
        $campos = $configuracoes->getByCodigo($codigoTipo);
        $opcoesNiveis = explode("\n", $campos['valor_referencia']);

        foreach ($opcoesNiveis as $opc) {
            $opcao = explode('|', $opc);
            if ($opcao[0] == $valor[0])
                return $opcao[1];
        }
    }

    public static function sexo($valor) {

        if ($valor == 'M')
            return "Masculino";
        else
            return "Feminino";
    }

    public static function profPredominante($valor) {

        if ($valor == 1)
            return "Academica";
        else
            return "Industria";
    }

}
