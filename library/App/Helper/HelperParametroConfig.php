<?php

class Zend_View_Helper_HelperParametroConfig extends Zend_View_Helper_Abstract {

    /**
     *  1 - Entidade Padrao
     *  2 - Centro de custo padrao
     * 11 - Descricao do conselho
     * 56 - Campo utilizado para login
     */
    public function helperParametroConfig($codigo, $entidadeId = 1) {

        if (!is_numeric($entidadeId) || $entidadeId <= 0)
            throw new Exception('O código da entidade está incorreto :: Deve ser numerico e maior que 0');

        if (is_numeric($codigo) && $codigo > 0) {

            $classParametroConfigTable = new ParametroConfig();
            $dados = $classParametroConfigTable->fetchAll("entidade_id = {$entidadeId} and codigo = {$codigo}")->toArray();

            if (empty($dados))
                throw new Exception("Nenhum registro foi encontrado para o codigo ({$codigo}) e entidade ({$entidadeId})!");

            // Valor definido para o codigo informado
            $valor = $dados[0]['definicao'];

            if (method_exists('Zend_View_Helper_HelperParametroConfig', "getValor{$codigo}")) {
                return $this->{"getValor{$codigo}"}($valor);
            } else {
                return $valor;
            }
        } else {
            throw new Exception('O código do parametro config está incorreto :: Deve ser numerico e maior que 0');
        }
    }

    public function getValor11($id) {
        $class = new ConselhoOrgaoEmissorTable();
        $rs = $class->fetchRow("id = {$id}")->toArray();

        if ($rs['sigla'] != '')
            return $rs['sigla'];
        else
            throw new Exception("A sigla do registro ({$id}) está vazia - Cadastre uma sigla no banco para que seja exibida corretamente!");
    }

    public function getValor56($id) {

        /* $class = new LoginCamposTable();
          $rs = $class->fetchRow("id = {$id}")->toArray();
          return $rs['column_name']; */
        $configuracaoTable = new ConfiguracaoTable();
        $configuracao = $configuracaoTable->findAllByCodigo(223);
//        $configuracao = end($configuracao);
//        Zend_Debug::dump($configuracao);die;
        $filter = new Zend_Filter_StringToLower();
        $configuracao['valor_referencia'] = !empty($configuracao['valor_referencia']) ? $configuracao['valor_referencia'] : 'email';
        return $filter->filter($configuracao['valor_referencia']);
    }

}
