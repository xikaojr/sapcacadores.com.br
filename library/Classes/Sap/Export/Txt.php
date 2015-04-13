<?php

class Itarget_Export_Txt {

    private $comAspas;
    private $separador;

    /**
     *
     * @param array $campos Cabecalho do relatorio, onde a chave eh o campo da tabela e valor o que ira aparecer como titulo da coluna
     * @param array $linhas Linhas da consulta
     * @param string $nomeArquivo Nome do arquivo a ser gerado
     * @param boolean $comAspas Delimitar campos com aspas
     */
    public function export($campos, $linhas, $nomeArquivo = 'relatorio', $comAspas = false, $separador = null) {

        $this->comAspas = ($comAspas) ? true : false;
        $this->separador = $separador;

        $conteudo = '';
        $conteudo .= $this->montarCabecalho($campos);
        $conteudo .= $this->montarCorpo($campos, $linhas);

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '_' . date('d-m-Y') . '.txt";');
        header('Content-Transfer-Encoding: binary');
        echo $conteudo;
        exit();
    }

    public function montarCabecalho($campos) {
        $cabecalho = '';

        foreach ($campos as $campo => $valor):
            $cabecalho .= ( ( $this->comAspas) ? '"' . $valor . '"' : $valor ). $this->separador;
            //$cabecalho .= "\t";
        endforeach;

        return $cabecalho;
    }

    public function montarCorpo($campos, $linhas) {
        $corpo = '';
        
        foreach ($linhas as $linha):
            $corpo .= "\n";
            foreach ($campos as $campo => $v):
                $valor = preg_replace('/\s/','',$linha[$campo]);
                $corpo .= ( ( $this->comAspas) ? '"' . $valor . '"' : $valor) . $this->separador;
                //$corpo .= "\t";
            endforeach;
        endforeach;

        return $corpo;
    }

}