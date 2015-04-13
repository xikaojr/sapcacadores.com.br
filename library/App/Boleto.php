<?php

final class App_Boleto {

    private $layout = null;
    private $file = null;
    private $digito_verificador = false;
    // aramazena os dados 
    private $data = null;
    private $error = array();

    /**
     * Layout que vai ser carregado pra verificação do arquivo retorno
     * 
     * @return void
     * @param $layout String Nome do layout que vai ser carregado
     */
    public function setLayout($layout) {
        $layout = strtr($layout, '-', '_');

        // verifica se foi preenchido o layout
        if (empty($layout)) {
            $this->error[] = 'O campo do layout está vazio!';
            return false;
        } elseif (!is_dir(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/Boleto/layouts/')) {
            $this->error[] = 'A pasta não existe!';
            return false;
        } elseif (!is_file(pathinfo(__FILE__, PATHINFO_DIRNAME) . "/Boleto/layouts/{$layout}.php")) {
            $this->error[] = 'O arquivo de layout não existe!';
            return false;
        } else {
            require pathinfo(__FILE__, PATHINFO_DIRNAME) . "/Boleto/layouts/{$layout}.php";
            $this->layout = new $layout();
        }
        return true;
    }

    /**
     * Define o arquivo que vai ser lido
     * 
     * @return void
     * @param $file String Arquivo que vai ser carregado
     */
    public function setFile($file) {
        // verifica se existe o arquivo
        $this->file = $file;
        return true;
    }

    /**
     * Define se vai ter digito verificador
     */
    public function setDv($arg = false) {
        if (is_bool($arg)) {
            $this->digito_verificador = $arg;
        }
    }

    /**
     * Lê o arquivo e retorna os dados
     * 
     * @return void
     * @param $dv Boolean[true,false] Define se tem digito verificador
     */
    public function readFile($parametros = array()) {
        
        $this->data = array();
        $this->data['detalhe'] = array();
        // define se tem digito verificador
        if ($this->digito_verificador == true) {
            $this->layout->setDV($this->digito_verificador);
        } elseif ($this->digito_verificador == false) {
            $this->layout->setDV($this->digito_verificador);
        }

        // verifica tem decimal ou nao
        $layouts = array('CNAB400', 'M_CED400');
        if (in_array($this->layout, $layouts)) {
            $this->setDecimal(false);
        }

        // pega o total de linhas por leitura
        $lines = $this->layout->getTotalLines();

        // total de linhas do arquivo retorno
        $total = count($this->file) - $lines['trailer'];

        for ($i = 0; $i < $total; $i++) {
            // armazena os dados
            $data = array();

            // verifica se a linha e menor ou igual ao total de linhas do header
            if ($i <= ($lines['header'] - 1)) {
                // pega os dados do header
                for ($c = 1; $c <= $lines['header']; $c++) {
                    // verifica se existe mais de uma linha por leitura
                    if ($c > 1 and $lines['header'] > 1) {
                        $i++;
                    }
                    // funde os resultados
                    $data = array_merge_recursive($data, $this->layout->header($c, $this->file[$i], $parametros['header']));
                }
                
                $this->data['header'] = $data;
            } else {
                // pega os dados do detalhe
                for ($c = 1; $c <= $lines['detalhe']; $c++) {
                    // verifica se existe mais de uma linha por leitura
                    if ($c > 1 and $lines['detalhe'] > 1) {
                        $i++;
                    }
                    
                    // funde os resultados
                    
                    $data = array_merge_recursive($data, $this->layout->detalhe($c, $this->file[$i], $parametros['detalhe']));
                }
                $this->data['detalhe'][] = $data;
            }
        }

        // verifica se tem multa
        if (count($this->data['detalhe']) > 0) {
            foreach ($this->data['detalhe'] as $k => $v) {
                // verifica se existe e se o valor é de multa
                if (isset($this->data['detalhe'][$k]['valor_multa']) and $k == 'valor_multa') {
                    // adiciona o valor da multa ao do juros
                    $this->data['detalhe'][$k]['valor_juros'] += $v['valor_multa'];
                    // apaga o valor da multa
                    unset($this->data['detalhe'][$k]['valor_multa']);
                }
            }
        }
        // verifica se tem erros
        if (count($this->layout->error) > 0) {
            $this->error = array_merge_recursive($this->error, $this->layout->error);
        }

        // define os valores
        $this->data['valores'] = $this->layout->getTotal();

        // limpa da memoria
        unset($i, $c, $total, $lines, $data);
    }

    /**
     * Pega os dados que foram gerados
     * 
     * @return Array
     */
    public function getDados() {
        return $this->data;
    }

    /**
     * Pega os erros
     * 
     * @return Array
     */
    public function getErrors() {
        return $this->error;
    }

    /**
     * Pega o total de erros gerados
     * 
     * @return Integer
     */
    public function getTotalErrors() {
        return count($this->error);
    }

    /**
     * Imprime os erros
     * 
     * @return void
     */
    public function printErrors() {
        foreach ($this->error as $e) {
            echo "{$e}<br />\n";
        }
    }

}
