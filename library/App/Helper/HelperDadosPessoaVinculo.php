<?php
class Zend_View_Helper_HelperDadosPessoaVinculo extends Zend_View_Helper_Abstract
{
    public function helperDadosPessoaVinculo($pessoaId,$enderecoId)
    {
        if( is_numeric($pessoaId) && is_numeric($enderecoId) ) {
            $classPessoaVinculoTable = new PessoaVinculo();
            $dados = $classPessoaVinculoTable->getDados($pessoaId, $enderecoId);
            return !empty($dados) ? end($dados) : array();
        } else {
            throw new Exception('Os parâmetros $pessoaId e $enderecoId devem ser passados');
        }
    }
}
