<?php

final class Sap_Date {

    /**
     * Converte a data $date de $type para $to
     * @param string $date
     *  A data a ser convertida
     * @param string $to
     *  O tipo conversor:
     *      br: para o formato dd/mm/yyyy
     *      sql: para o formato yyyy-mm-dd
     * @param string $type
     *  O tipo (date ou datetime)
     * @return string
     *  A data convertida
     */
    public static function convertDate($date, $to, $type) {
        $return = (string) '';
        switch ($type) {
            case "date":
                switch ($to) {
                    case "sql":
                        $date = self::isValidDate($date, "br");
                        $return = self::dateToSql($date);
                        break;
                    case "br":
                        $date = self::isValidDate($date, "sql");
                        $return = self::dateToBr($date);
                        break;
                    default :
                        throw new UtilsException(sprintf("Parâmetro para formato inválido (%s). Permitidos: %s, %s", $to, 'sql', 'br'));
                }
                break;
            case "datetime":
                switch ($to) {
                    case "sql": $return = self::datetimeToSql($date);
                        break;
                    case "br": $return = self::datetimeToBr($date);
                        break;
                    default :
                        throw new UtilsException(sprintf("Parâmetro para formato inválido (%s). Permitidos: %s, %s", $to, 'sql', 'br'));
                }
                break;
            default :
                throw new UtilsException(sprintf("Parâmetro para tipo inválido (%s). Permitidos: %s, %s", $type, 'date', 'datetime'));
        }

        return $return;
    }

    private static function validateUnknownDate($date) {
        $return = (boolean) false;
        $validators = array(
            'ISO' => '([0-9]{4})(-|/|\.)([0-9]{1,2})(-|/|\.)([0-9]{1,2})',
            'BR' => '([0-9]{1,2})(-|/|\.)([0-9]{1,2})(-|/|\.)([0-9]{4})'
        );

        foreach ($validators as $pattern) {
            if (ereg($pattern, $date)) {
                $return = true;
                break;
            }
        }

        return $return;
    }

    /**
     * Verifica se uma data é válida
     * @param string $date
     *  A data a ser avaliada
     * @param string $type
     *  O tipo comparador:
     *      br: para o formato dd/mm/yyyy
     *      sql: para o formato yyyy-mm-dd
     * @return string
     *  Em caso de sucesso, retorna a data inalterada
     */
    public static function isValidDate($date, $type = null) {
        $valid_date = (boolean) false;
        if (is_null($type)) {
            try {
                $valid_date = self::validateUnknownDate($date);
            } catch (UtilsException $e) {
                throw $e;
            }
        } else {
            $method = 'isValidDateIn' . ucfirst($type);
            if (method_exists(self, $method)) {
                try {
                    $valid_date = self::$method($date);
                } catch (UtilsException $e) {
                    throw $e;
                }
            } else {
                throw new UtilsException('Formato de data desconhecida.');
            }
        }

        return $valid_date;
    }

    /**
     * Converte $datetime para o formato brasileiro
     * @param string $datetime
     * @return string
     */
    private static function datetimeToBr($datetime) {
        return implode(
                        "/", array_reverse(
                                explode(
                                        "-", substr($datetime, 0, 10)
                                )
                        )
                )
                . " " . substr($datetime, 10);
    }

    /**
     * Converte $datetime para o formato sql
     * @param string $datetime
     * @return string
     */
    private static function datetimeToSql($datetime) {
        return implode(
                        "-", array_reverse(
                                explode(
                                        "/", substr($datetime, 0, 10)
                                )
                        )
                ) . " " . substr($datetime, 10);
    }

    /**
     * Converte $date para o formato brasileiro
     * @param string $date
     * @return string
     */
    private static function dateToBr($date) {
        $pattern = "([0-9]{4})(-|/|\.)([0-9]{1,2})(-|/|\.)([0-9]{1,2})";
        if (ereg($pattern, $date, $return)) {
            return $return[5] . '/' . $return[3] . '/' . $return[1];
        } else {
            throw new UtilsException('A data fornecida é inválida');
        }
    }

    /**
     * Converte $date para o formato sql
     * @param string $date
     * @return string
     */
    private static function dateToSql($date) {
        $pattern = "([0-9]{1,2})(-|/|\.)([0-9]{1,2})(-|/|\.)([0-9]{4})";
        if (ereg($pattern, $date, $return)) {
            return $return[5] . '/' . $return[3] . '/' . $return[1];
        } else {
            throw new UtilsException('A data fornecida é inválida');
        }
    }

    /**
     * Escreve a data indicada por extenso.
     * @param string $date Data para ser escrita por extenso no formato: yyyy-mm-dd. Caso não entre com argumento, imprime-se a data de hoje
     * @return string Retorna a data por extenso
     */
    public function dateToText($date = null) {

        $semana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
        $mes = array(1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

// Se usuário entrar com a Data, escreve a data indicada
        if (!is_null($date)) {

            $data_semana = date('w', strtotime($date));
            $data_dia = date('d', strtotime($date));
            $data_mes = date('n', strtotime($date));
            $data_ano = date('Y', strtotime($date));

// Caso contrário, põe a data de hoje
        } else {

            $dataHoje = date('Y-m-d');

            $data_semana = date('w', strtotime($dataHoje));
            $data_dia = date('d', strtotime($dataHoje));
            $data_mes = date('n', strtotime($dataHoje));
            $data_ano = date('Y', strtotime($dataHoje));
        }

        return $data_dia . ' de ' . $mes[$data_mes] . ' de ' . $data_ano . "<br />" . $semana[$data_semana];
    }

    public static function ultimaDataAnterior($d = false) {

        $dataAnterior = strtotime("{$d}-1 month");
        $dataAnterior = Itarget_Date::ultimoDiaMes(date("Y-m-d", $dataAnterior));

        return $dataAnterior;
    }

    public static function ultimoDiaMes($d = false, $tipo = "T") {

        if (!$d)
            $d = date("Y-m-d");

        list($a, $m, $d) = explode('-', $d);
        $ultDia = date("d", mktime(0, 0, 0, $m + 1, 1, $a) - 1);

        if ($tipo == "T")
            return "$a-$m-$ultDia";
        else
            return $ultDia;
    }

    public static function temposSegundos($segundos = false) {

        $tempo = array();
        $tempo[60] = 'Minutos';
        $tempo[3600] = 'Horas';
        $tempo[86400] = 'Dias';
        $tempo[604800] = 'Semanas';

        if ($segundos == false)
            return $tempo;
        else
            return $tempo[$segundos];
    }

    public static function tempoParcelas($tempo = false) {

        $tempo_parcelas = array();
        $tempo_parcelas['weekly'] = 'Semanas';
        $tempo_parcelas['15days'] = 'Quinzenas';
        $tempo_parcelas['monthly'] = 'Meses';
        $tempo_parcelas['bimonthly'] = 'Bimestres';
        $tempo_parcelas['trimonthly'] = 'Trimentres';
        $tempo_parcelas['sixmonthly'] = 'Semestres';
        $tempo_parcelas['yearly'] = 'Anual';

        if ($tempo == false)
            return $tempo_parcelas;
        else
            return $tempo_parcelas[$tempo];
    }

    public static function convertPubDate($format, $pubdate) {
        @$data = date($format, strtotime($pubdate));

        return $data;
    }

    public static function porExtenso($data, $lang = 'pt-BR') {
        $dt = new Zend_Date($data, Zend_Date::ISO_8601, $lang);

        if ($lang == 'pt-BR')
            $data = $dt->toString('EEEE, d') . ' de ' . $dt->toString('MMMM') . ' de ' . $dt->toString('Y');
        else
            $data = $dt->toString('MMMM') . ', ' . $dt->toString('EEEE, d') . ' of ' . $dt->toString('Y');

        return $data;
    }

    public static function diaSemana($data) {
        $data = str_replace('/', '-', $data);

        $w = date('w', strtotime($data));

        $diaSemana = array(
            '0' => 'Domingo',
            '1' => 'Segunda-feira',
            '2' => 'Terça-feira',
            '3' => 'Quarta-feira',
            '4' => 'Quinta-feira',
            '5' => 'Sexta-feira',
            '6' => 'Sabado',
        );

        return $diaSemana[$w];
    }

    public static function formatar($data, $formato = 'd/m/Y') {

        if (empty($data))
            return null;

        $data = str_replace('/', '-', $data);

        $dt = date($formato, strtotime($data));

        return $dt;
    }

    public static function ptBr($data, $hora = true) {
        $dt = new Zend_Date($data, 'BR');
        $data = $dt->toString('dd/MM/y');

        if ($hora == true)
            $data .= " às " . $dt->toString('HH:mm');

        return $data;
    }

    public static function converterData($dados = array(), $pattern = null, $regex = App_Db_Table_Abstract::REGEXP_DATA_BR) {

        foreach ($dados as $key => $value) {
            if (!empty($dados[$key])) {
                if (Zend_Date::isDate($value, 'yyy-MM-dd HH:mm:ss')) {
                    $value = preg_replace($regex, "$1-$2-$3", $value);
                }
            } else {
                $dados[$key] = null;
            }
        }

        return $dados;
    }

    public static function converterUnicDate($data, $regex = App_Db_Table_Abstract::REGEXP_DATA) {

        if (Zend_Date::isDate($data, 'yyy-MM-dd HH:mm')) {
            $data = preg_replace($regex, '$1-$2-$3', $data);
        } else {
            $data = null;
        }

        return $data;
    }

    public static function toEng($dados = array()) {

        foreach ($dados as $key => $value) {
            if (!empty($value)) {
                if (Zend_Date::isDate($value, 'd/m/Y') && strpos($value, '/')) {
                    $dt = new Zend_Date($value);
                    $dados[$key] = $dt->toString('Y-m-d');
                } else if (Zend_Date::isDate($value, 'd/m/Y h:m:s')) {
                    $dt = new Zend_Date($value);
                    $dados[$key] = $dt->toString('Y-m-d  h:m:s');
                }
            }
        }

        return $dados;
    }

    public static function toPtBr($dados = null) {

        if (is_array($dados) && !empty($dados)) {

            foreach ($dados as $key => $value) {
                if (!empty($dados[$key])) {
                    if (Zend_Date::isDate($value, 'Y-M-d')) {
                        $dt = new Zend_Date($value);
                        $dados[$key] = $dt->toString('d/m/Y');
                    } else if (Zend_Date::isDate($value, 'Y-M-d h:m:s')) {
                        $dt = new Zend_Date($value);
                        $dados[$key] = $dt->toString('d/m/Y  h:m:s');
                    }
                } else {
                    $dados[$key] = null;
                }
            }
        } else {
            if (Zend_Date::isDate($dados, 'Y-M-d')) {
                $dt = new Zend_Date($dados);
                $dados = $dt->toString('d/m/Y');
            } else if (Zend_Date::isDate($dados, 'Y-M-d h:m:s')) {
                $dt = new Zend_Date($dados);
                $dados = $dt->toString('d/m/Y  h:m:s');
            }
        }

        return $dados;
    }

    public static function enEn($data) {
        if (empty($data))
            return null;

        $dt = new Zend_Date($data);
        $data = $dt->toString('Y-m-d');
        return $data;
    }

    public static function getNumberMonth($mes) {
        $meses = array(
            '0' => '01',
            '1' => '02',
            '2' => '03',
            '3' => '04',
            '4' => '05',
            '5' => '06',
            '6' => '07',
            '7' => '08',
            '8' => '09',
            '9' => '10',
            '10' => '11',
            '11' => '12'
        );

        return $meses[$mes];
    }

    public static function getMonth($mes = null) {
        $meses = array(
            '1' => 'Janeiro',
            '2' => 'Fevereiro',
            '3' => 'Março',
            '4' => 'Abril',
            '5' => 'Maio',
            '6' => 'Junho',
            '7' => 'Julho',
            '8' => 'Agosto',
            '9' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        );

        if ($mes != null)
            return $meses[$mes];
        else
            return $meses;
    }

    public static function alterarDias($dias, $data) {
        $data = explode('-', $data);

        $dia = $data[2];
        $mes = $data[1];
        $ano = $data[0];
        $dataFinal = mktime(24 * $dias, 0, 0, $mes, $dia, $ano);
        $dataFormatada = date('Y-m-d', $dataFinal);

        return $dataFormatada;
    }

    public static function alterarMinutos($minutos, $horas) {
        $horaNova = strtotime($horas . " " . $minutos . " minutes");

        return date("H:i", $horaNova);
    }

    public static function somarTempo($data, $tempo) {
        $dataNow = (strtotime($data) + $tempo);

        return date("Y-m-d H:i", $dataNow);
    }

    public static function somarDias($data, $dias) {
        $dataNow = date("Y-m-d", strtotime(Class_Date::somarTempo($data, $dias * 86400)));

        return $dataNow;
    }

    public static function faltamDias($data) {
        $data = explode("-", $data);

        $dias = ceil((mktime(0, 0, 0, $data[1], $data[2], $data[0]) - time()) / 86400);

        if ($dias <= 0)
            return false;

        return "(faltam {$dias} dias)";
    }

    public static function getSemestralidade(Zend_Date $data) {
        $translate = Zend_Registry::get('translate');

        $semestralidade = 2;
        $d = (int) $data->toString('MM');
        $ano = ((int) $data->toString('YYYY') - 1);
        if (in_array($d, array(6, 7, 8, 9, 10, 11, 12))) {
            $semestralidade = 1;
            $ano = $data->toString('YYYY');
        }

        return $semestralidade . ' ' . $translate->_('Semestralidade') . ' ' . $ano;
    }

    /**
     * Compara a $dataReg em um intervalo de tempo
     * @param (datetime) $dataReg - Formato d/m/y h:i:s. Data a ser coparada
     * @param (datetime) $intervaloInicio - Formato d/m/y h:i:s. Data inicial do intervalo
     * @param (datetime) $intervaloFim - Formato d/m/y h:i:s. Data final do intervalo
     * @return (array) $retorno = array('status' => boolean, 'msg' => string)
     */
    public function verificaDataEmIntervalo($dataReg = null, $intervaloInicio = null, $intervaloFim = null) {
        $retorno = array('status' => false, 'msg' => '');
        if (!$dataReg || !$intervaloInicio || !$intervaloFim) {
            $retorno['status'] = false;
            $retorno['msg'] = 'Informe os parametros corretamente';
        }

        $dataHoraInicio = explode(" ", $intervaloInicio);
        $data = explode("/", $dataHoraInicio[0]);
        $hora = explode(":", $dataHoraInicio[1]);
        $timeInicio = mktime($hora[0], $hora[1], $hora[2], $data[1], $data[0], $data[2]);

        $dataHoraFim = explode(" ", $intervaloFim);
        $data = explode("/", $dataHoraFim[0]);
        $hora = explode(":", $dataHoraFim[1]);
        $timeFim = mktime($hora[0], $hora[1], $hora[2], $data[1], $data[0], $data[2]);

        $dataHora = explode(" ", $dataReg);
        $data = explode("/", $dataHora[0]);
        $hora = explode(":", $dataHora[1]);
        $time = mktime($hora[0], $hora[1], $hora[2], $data[1], $data[0], $data[2]);

        if (($time < $timeInicio) || ($time > $timeFim)) {
            $retorno['status'] = true;
            $retorno['msg'] = 'Registro fora do intervalo';
        }
        return $retorno;
    }

}

?>
