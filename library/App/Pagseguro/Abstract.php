<?php

/**
 * Itarget_Pagseguro_Pagseguro
 *
 * Classe de manipulação para o retorno do post do pagseguro
 *
 * @package PagSeguro
 */
class App_Pagseguro_Abstract {

    const APROVADO = 'aprovado';
    const AguardandoPagamento = 'Aguardando Pagto';
    const VERIFICADO = 'VERIFICADO';
    const FALTO = 'FALSO';
    
    //STATUS DO RETORNO DO PAGSEGURO
    const AGUARDANDO_PAGAMENTO = 1;
    const EM_ANALISE = 2;
    const PAGA = 3;
    const DISPONIVEL = 4;
    const EM_DISPUTA = 5;
    const DEVOLVIDA = 6;
    const CANCELADA = 7;

    /**
     * @var Object Classe que representa a geracao de log
     */
    protected $log;

    /**
     * set Class log
     * @param Itarget_Log_File $log
     * @return \Itarget_Pagseguro_Abstract
     */
    public function setLog(App_Log_File $log) {
        $this->log = $log;
        return $this;
    }

    /**
     * Retorna Class log
     * @return \Itarget_Log_File
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * 
     * @param array $dados
     * @param type $tipo
     */
    public function requisicao(array $dados, $tipo) {

        if (null !== $this->getLog()) {
            $texto = array();
            foreach ($dados as $i => $v) {
                $texto[] = "{$i} = $v \n";
            }
            $this->getLog()->write("{$tipo}:\n" . implode('', $texto));
        }
    }

    /**
     * Verifica informacao junto a cielo
     * @param string $token chave de identificacao do cliente
     * @return string
     */
    public function notificationPost($token) {
        $postdata = 'Comando=validar&Token=' . $token;
        foreach ($_POST as $key => $value) {
            $valued = $this->clearStr($value);
            $postdata .= "&$key=$valued";
        }
        return $this->verify($postdata);
    }

    /**
     * Tratando informacoes para serem enviadas ao pagseguro
     * @param string $str informacao
     * @return string
     */
    private function clearStr($str) {
        if (!get_magic_quotes_gpc()) {
            $str = addslashes($str);
        }
        return $str;
    }

    /**
     * Verifica se os dados foram enviados pelo pagseguro
     * @param string $data informacao sobre a transacao
     * @return string
     */
    private function verify($data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://pagseguro.uol.com.br/pagseguro-ws/checkout/NPI.jhtml");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = trim(curl_exec($curl));
        curl_close($curl);
        return $result;
    }

}
