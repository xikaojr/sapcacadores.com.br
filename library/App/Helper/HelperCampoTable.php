<?php
class Zend_View_Helper_HelperCampoTable extends Zend_View_Helper_Abstract
{
    // Os inputs devem esta outros[]
    public function helperCampoTable(array $campo)
    {
        $campoHtml = '';
        
        switch( (int) $campo['tipo'] ) { 
            case CampoTabela::TIPO_S_N:
                $campoHtml .= 'Nao implementado';
                break; 
            case CampoTabela::TIPO_INT: 
                $campoHtml .= 'Nao implementado';
                break; 
            case CampoTabela::TIPO_DATA: 
                $campo['valor_tipo_' . $campo['tipo']] = join('/', array_reverse(explode('-', $campo['valor_tipo_' . $campo['tipo']])));
                $campoHtml .= '<input type="text" name="outros['.$campo['id_campo_tabela_valor'].'][valor_tipo_' . CampoTabela::TIPO_DATA . ']" value="'. $campo['valor_tipo_' . $campo['tipo']] .'" class="pequeno date" />';
                break; 
            case CampoTabela::TIPO_VARCHAR:
                $campoHtml .= '<input type="text" name="outros['.$campo['id_campo_tabela_valor'].'][valor_tipo_4]" value="'. $campo['valor_tipo_' . $campo['tipo']] .'" class="medio" />';
                break; 
            case CampoTabela::TIPO_FINANCEIRO: 
                $campoHtml .= 'Nao implementado';
                break; 
            default:
                throw new Exception('O tipo informado nao foi localizado em: helperCampoTable(array $campo) - tipo: ' . $campo['tipo']);
        }
        
        return $campoHtml;
    }
}