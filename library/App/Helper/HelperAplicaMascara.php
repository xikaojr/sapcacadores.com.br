<?php

class Zend_View_Helper_HelperAplicaMascara extends Zend_View_Helper_Abstract {

    // Os inputs devem esta outros[]
    public function helperAplicaMascara($valor, $tipo) {

        switch ($tipo) {
            case 'ddd+fone':
                return $this->mask($valor, "(##) ####-####");
                break;
            case 'fone':
                return $this->mask($valor, "####-####");    
                break;
            case 'cep':
                return $this->mask($valor, "#####-###");
                break;
            case 'cpf':
                return $this->mask($valor, "###.###.###-##");
                break;
            case 'cnpj':
                return $this->mask($valor, "##.###.###/####-##");
                break;
            default:
                throw new Exception('O tipo informado nao foi localizado em: helperAplicaMascara');
        }

        return $campoHtml;
    }

    private function mask($val, $mask) {
        $maskared = '';
        $k = 0;
        
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
}