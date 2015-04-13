<?php

/**
 * Possui algumas funcionalidades para o tratamento de valores monetarios
 */
class App_Helper_Moeda {

    /**
     * Formata um valor para que seja exibido na tela
     * @param double $valor Valor a ser formatado
     * @param boolean $simbolo Exibir o simbolo
     * @return string
     */
    public static function formatar($valor, $simbolo = true) {
        $valor = (double) $valor;
//        die($valor);
        return ($simbolo) ?
                self::simbolo() . ' ' . number_format($valor, 2, ',', '.') :
                number_format($valor, 2, ',', '.');
    }

    public static function simbolo() {
        return 'R$';
    }

    /**
     * Formata uma string para um valor double, removendo os pontos e substituindo
     * a virgula por ponto
     * @param string $valor Valor a ser desformatado
     * @return double
     */
    public static function desformatar($valor, $formatar = false) {
        $valor = str_replace(array('R$', ' ', '.', ','), array('', '', '', '.'), $valor);
        if ($formatar) {
            return number_format($valor, 2, ',', '.');
        }
        return $valor;
    }

    public static function desformatarFromRetorno($valor) {
        return str_replace(',', '', $valor);
    }

    public static function somenteNumero($valor) {
        if (empty($valor)) {
            return false;
        }
        return preg_replace('/[^0-9]/', '', number_format($valor, 2, '', ''));
    }

    public static function porExtenso($valor = 0, $maiusculas = false) {

        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
            "quatrilhões");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
            "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
            "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
            "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
            "sete", "oito", "nove");

        $z = 0;
        $rt = "";

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for ($i = 0; $i < count($inteiro); $i++)
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];

        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                    $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++; elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        if (!$maiusculas) {
            return($rt ? $rt : "zero");
        } else {

            if ($rt)
                $rt = ereg_replace(" E ", " e ", ucwords($rt));
            return (($rt) ? ($rt) : "Zero");
        }
    }

}
