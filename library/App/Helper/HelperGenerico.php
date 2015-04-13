<?php

class Zend_View_Helper_HelperGenerico extends Zend_View_Helper_Abstract {

    public function HelperGenerico() {
        return $this;
    }

    public function helperOcutarBotoes($codigo, $centroCustoId, $pessoa_id) {
        $configuracaoTable = new Configuracao();
        $config = $configuracaoTable->findAllByCodigoAndCentroCustoId($codigo, $centroCustoId);
        // implemente aqui os possíveis eventos 
        if ($config['valor_referencia'] == 1) {
            $trabalhos_autores = new TrabalhosAutores();
            switch ($codigo) {
                case 167:
                    $rs = $trabalhos_autores->autorPossuiTrabalhos($centroCustoId, $pessoa_id);
                    // caso possua trabalho remove inscricao
                    return $rs > 0 ? false : true;
                    break;
                case 168:
                    $rs = $trabalhos_autores->autorPossuiTrabalhos($centroCustoId, $pessoa_id);
                    // caso nao possua trabalhos remove o botao de trabalhos
                    return $rs == 0 ? false : true;
                    break;
            }
        } else {
            return true;
        }
    }

    public function helperObterTexto($codigo, $centroCustoId, $idioma, $retornoHtml = true) {
        $configuracaoTable = new Configuracao();
        $config = $configuracaoTable->findAllByCodigoAndCentroCustoId($codigo, $centroCustoId);
        return $this->trataTextoIdioma($config['valor_referencia'], $idioma, $retornoHtml);
    }

    public function trataTextoIdioma($valor, $idioma, $retornoHtml = true) {
        $html = '';
        // trata idioma
        if (!empty($valor)) {
            // caracter invisivel
            $enter = '
';
            $valor = str_replace($enter, '', $valor);
            $idioma = strtoupper($idioma);
            // pega o valor das tags de idiomas            
            preg_match('/<' . $idioma . '>(.*?)<\/' . $idioma . '>/', $valor, $texto);

            if ($retornoHtml) {
                $html = isset($texto[1]) ? "class='descTip' title='" . utf8_encode($texto[1]) . "'" : '';
            } else {
                $html = isset($texto[1]) ? $texto[1] : '';
            }
        }
        return $html;
    }

}
