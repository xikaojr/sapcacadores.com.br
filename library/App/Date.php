<?php

/**
 * Sistemas Desenvolvimento
 * 
 * PHP Versao 5.3
 * 
 * @copyright (c) 2014, Itarget tecnologia
 * @link http://www.itarget.com.br
 */

/**
 * Class para munipular todas as datas do sistema
 */
final class App_Date {

    /**
     * Retorna a data anterior
     * @param string $d data
     * @return string
     */
    public static function ultimaDataAnterior($d = false) {
        $dataAnterior = strtotime("{$d}-1 month");
        return App_Date::ultimoDiaMes(date("Y-m-d", $dataAnterior));
    }

    /**
     * Retorna a data/ultima dia do mes
     * @param mixed $d data a ser considerado
     * @param string $tipo OPCIONAL se for igual a T retorna ano-mes-ultimoDiaMes se nao retorna somente o ultimo dia
     * @return string
     */
    public static function ultimoDiaMes($d = false, $tipo = "T") {

        if (!$d)
            $d = date("Y-m-d");

        list($a, $m, $d) = explode('-', $d);
        $ultDia = date("d", mktime(0, 0, 0, $m + 1, 1, $a) - 1);

        return $tipo == "T" ? "$a-$m-$ultDia" : $ultDia;
    }

    /**
     * Retorna o tempo em formaato de segundos
     * @param mixed $segundos tipo
     * @return string
     */
    public static function temposSegundos($segundos = false) {

        $tempo = array();
        $tempo[60] = 'Minutos';
        $tempo[3600] = 'Horas';
        $tempo[86400] = 'Dias';
        $tempo[604800] = 'Semanas';

        return $segundos == false ? $tempo : $tempo[$segundos];
    }

    /**
     * Retorna os tempos em portugues
     * @param string $tempo OPCIONAL tempo
     * @return mixed caso tempo nao for false retorna string caso contrario retorna o array com todos os resultados
     */
    public static function tempoParcelas($tempo = false) {

        $tempo_parcelas = array();
        $tempo_parcelas['weekly'] = 'Semanas';
        $tempo_parcelas['15days'] = 'Quinzenas';
        $tempo_parcelas['monthly'] = 'Meses';
        $tempo_parcelas['bimonthly'] = 'Bimestres';
        $tempo_parcelas['trimonthly'] = 'Trimentres';
        $tempo_parcelas['sixmonthly'] = 'Semestres';
        $tempo_parcelas['yearly'] = 'Anual';

        return $tempo == false ? $tempo_parcelas : $tempo_parcelas[$tempo];
    }

    /**
     * Formata data usando date
     * @param string $format
     * @param int $pubdate
     * @return string
     */
    public static function convertPubDate($format, $pubdate) {
        @$data = date($format, strtotime($pubdate));

        return $data;
    }

    /**
     * Retorna a data passado por extenso
     * @param string $data data 
     * @param string $lang idioma da data
     * @return string
     */
    public static function porExtenso($data, $lang = 'pt-BR') {
        $dt = new Zend_Date($data, Zend_Date::ISO_8601, $lang);

        if ($lang == 'pt-BR')
            $data = $dt->toString('EEEE, d') . ' de ' . $dt->toString('MMMM') . ' de ' . $dt->toString('Y');
        else
            $data = $dt->toString('MMMM') . ', ' . $dt->toString('EEEE, d') . ' of ' . $dt->toString('Y');

        return $data;
    }

    /**
     * Retorna o dia da semana da data
     * @param string $data data 
     * @return string
     */
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

    /**
     * Formata data
     * @param string $data data
     * @param string $formato OPCIONAL formato da data
     * @return mixed caso data vazia retorna null caso contrario retorna string
     */
    public static function formatar($data, $formato = 'd/m/Y') {

        if (empty($data))
            return null;

        $data = str_replace('/', '-', $data);

        return date($formato, strtotime($data));
    }

    /**
     * Retorna da data/hora no formato ptbr
     * @param string $data data 
     * @param bool $hora retorna hora
     * @return string
     */
    public static function ptBr($data, $hora = true) {
        $dt = new Zend_Date($data, 'BR');

        $data = $dt->toString('d/m/Y');

        if ($hora == true)
            $data .= " às " . $dt->toString('HH:mm');

        return $data;
    }

    /**
     * Retorna da data/hora no formato ingles
     * @param string $data data 
     * @return string
     */
    public static function enEn($data) {
        if (empty($data))
            return null;

        $dt = new Zend_Date($data);
        return $dt->toString('yy-m-d');
    }

    /**
     * Retorna o numero do mes
     * @param int $mes
     * @return string
     */
    public static function getNumberMonth($mes) {
        $meses = array('0' => '01', '1' => '02', '2' => '03', '3' => '04', '4'
            => '05', '5' => '06', '6' => '07', '7' => '08', '8' => '09', '9'
            => '10', '10' => '11', '11' => '12'
        );

        return $meses[$mes];
    }

    /**
     * Retorna o nome do mes referente ao numero
     * @param string $mes OPCIONAL numero do mes
     * @return mixed
     */
    public static function getMonth($mes = null) {
        $meses = array('1' => 'Janeiro', '2' => 'Fevereiro', '3' => 'Março', '4' => 'Abril',
            '5' => 'Maio', '6' => 'Junho', '7' => 'Julho', '8' => 'Agosto', '9' => 'Setembro',
            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        );

        return $mes != null ? $meses[$mes] : $meses;
    }

    /**
     * Altera o dia de uma data
     * @param int $dias
     * @param string $data data formato Y-m-d
     * @return string
     */
    public static function alterarDias($dias, $data) {
        $data = explode('-', $data);

        $dia = $data[2];
        $mes = $data[1];
        $ano = $data[0];
        $dataFinal = mktime(24 * $dias, 0, 0, $mes, $dia, $ano);
        return date('Y-m-d', $dataFinal);
    }

    /**
     * Altera minutos de uma hora
     * @param type $minutos
     * @param type $horas
     * @return type
     */
    public static function alterarMinutos($minutos, $horas) {
        $horaNova = strtotime($horas . " " . $minutos . " minutes");

        return date("H:i", $horaNova);
    }

    /**
     * Soma um tempo a data
     * @param string $data data 
     * @param string $tempo temoi a ser somado a data
     * @return string
     */
    public static function somarTempo($data, $tempo) {
        $dataNow = (strtotime($data) + $tempo);

        return date("Y-m-d H:i", $dataNow);
    }

    /**
     * Soma dia a data passada
     * @param string $data data
     * @param int $dias dia(s) a ser somado
     * @return string
     */
    public static function somarDias($data, $dias) {
        return date("Y-m-d", strtotime(Class_Date::somarTempo($data, $dias * 86400)));
    }

    /**
     * Retorna a quantidade de dias faltando
     * @param string $data data
     * @return boolean
     */
    public static function faltamDias($data) {
        $data = explode("-", $data);

        $dias = ceil((mktime(0, 0, 0, $data[1], $data[2], $data[0]) - time()) / 86400);

        if ($dias <= 0)
            return false;

        return "(faltam {$dias} dias)";
    }

    /**
     * Retorna a semestralidade referente ao mes em questao
     * @param Zend_Date $data
     * @return string
     */
    public static function getSemestralidade(Zend_Date $data) {
        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

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

    /**
     * Formata data conforme localizacao
     * @param string $data data a ser formatada
     * @return mixed null caso data vazio string caso contrario
     */
    public static function formatarDataPorLocalidade($data) {

        if (empty($data))
            return null;

        $translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

        $dt = new Zend_Date($data, null, $translate->getLocale());
        //estava dando algum erro no formato automatico por localizacao do zend
        //fiz esse if mais futuramente temos que analisar melhor esse caso
        if ($translate->getLocale() == 'pt_BR') {
            $dt = $dt->toString('d/m/Y');
        } else {
            $dt = $dt->toString('Y.m.d');
        }
        return $dt;
    }

}
