<?php

/**
 * Itarget_Paypal_Abstract
 * 
 * @copyright (c) 2014, Itarget tecnologia
 * @package Paypal
 */

/**
 * Classe de manipulação para o requisicao do paypal
 * Retiarada do proprio site do paypal
 * @link https://www.paypal-brasil.com.br/desenvolvedores/code-samples/ Paypal
 * @author Edilson Rodrigues
 */
class Itarget_Paypal_Abstract {

    /**
     * @var Object Classe que representa a geracao de log
     */
    protected $log;

    /**
     * @var sigla do cliente
     */
    protected $_clt;

    /**
     * @var centro de custo
     */
    protected $_centroCusto;

    /**
     * Significa que a transação está completa e o valor foi depositado 
     * em sua conta. Você pode entregar os produtos para o cliente 
     * ou liberar acesso à alguma área exclusiva, ou conteúdo digital.
     * @var string
     */
    const CONCLUIDO = 'Completed';

    /**
     * Significa que a transação foi negada. Esse valor apenas ocorre, 
     * caso o status anterior tenha sido Pending e o campo pending_reason 
     * tenha sido algum dos valores descritos no campo 
     * Fraud_Management_Filters_x.
     * @var string
     */
    const NEGADO = 'Denied';

    /**
     * Significa que a autorização expirou e não pode mais ser capturada.
     * @var string
     */
    const EXPIRADO = 'Expired';

    /**
     * Significa que o pagamento falhou. Esse valor apenas ocorre,
     * caso o cliente tenha utilizado sua conta em bancária 
     * para fazer o pagamento.
     * @var string
     */
    const FALHOU = 'Failed';

    /**
     * Significa que o pagamento está pendente de revisão. 
     * Caso esse campo ocorra, verifique o campo pending_reason 
     * para mais detalhes sobre o motivo.
     * @var string
     */
    const PENDENTE = 'Pending';

    /**
     * Significa que um reembolso foi emitido.
     * @var string
     */
    const REEMBOLSO = 'Refunded';

    /**
     * Significa que um pagamento foi revertido por causa de um chargeback 
     * ou qualquer outro motivo. O valor que havia sido pago foi removido 
     * da conta do vendedor e devolvido para a conta do cliente. 
     * O motivo da reversão pode ser encontrado no campo ReasonCode.
     * @var string
     */
    const REVERTIDO = 'Reversed';

    /**
     * Significa que um pagamento foi aceito.
     * @var string
     */
    const PRECESSANDO = 'Processed';

    /**
     * Significa que uma autorização foi cancelada.
     * @var string
     */
    const CANCELADO = 'Voided';

    /**
     * set Class log
     * @param Itarget_Log_File $log
     * @return \Itarget_Pagseguro_Abstract
     */
    public function setLog(Itarget_Log_File $log) {
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
     * Retorna a sigla do cliente
     * @return String
     */
    public function getClt() {
        return $this->_clt;
    }

    /**
     * retorno o centro de custo
     * @return int
     */
    public function getCentroCusto() {
        return $this->_centroCusto;
    }

    /**
     * set a sigla do cliente
     * @param string $_clt sigla do cliente
     * @return \Itarget_Paypal_Abstract
     */
    public function setClt($_clt) {
        $this->_clt = $_clt;
        return $this;
    }

    /**
     * Set o centro de custo
     * @param int $_centroCusto centro de custo
     * @return \Itarget_Paypal_Abstract
     */
    public function setCentroCusto($_centroCusto) {
        $this->_centroCusto = (int) $_centroCusto;
        return $this;
    }

    /**
     * Verifica se uma notificação IPN é válida, fazendo a autenticação
     * da mensagem segundo o protocolo de segurança do serviço.
     *
     * FUNÇÃO TIRADA DO SITE PAYPAL
     * @link thtps://www.paypal-brasil.com.br/desenvolvedores/tutorial/guia-de-integracao-com-ipn/ 
     * 
     * @param array $message Um array contendo a notificação recebida.
     * @return boolean TRUE se a notificação for autência, ou FALSE se não for.
     */
    function isIPNValid(array $message) {
        $endpoint = 'https://www.paypal.com';

        if (isset($message['test_ipn']) && $message['test_ipn'] == '1') {
            $endpoint = 'https://www.sandbox.paypal.com';
        }

        $endpoint .= '/cgi-bin/webscr?cmd=_notify-validate';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($message));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $errno = curl_errno($curl);

        curl_close($curl);

        return empty($error) && $errno == 0 && $response == 'VERIFIED';
    }

    /**
     * Grava a mensagem da notificação em um arquivo *.txt para
     * verificação de duplicidade e, se for o caso, análise das
     * notificações recebidas.
     * 
     * @param Itarget_Log_File $logFile Class para gerar log
     * @param array $message Mensagem IPN
     * @return mixed boonlean caso sucesso ou Exception caso erro
     */
    function logIPN(Itarget_Log_File $logFile, array $message) {

        $logFile->setDirLogFile(APP_PATH . "../data/log/cartao/{$this->getClt()}/{$this->getCentroCusto()}")->setLogFile("paypal.log");

        $ipn = array_merge(array(
            'txn_id' => null,
            'txn_type' => null,
            'payment_status' => null,
            'pending_reason' => null,
            'reason_code' => null,
            'custom' => null,
            'invoice' => null
                ), $message);

        $notification = serialize($message);
        $hash = md5($notification);
        $conteudo = '';
        foreach ($ipn as $k => $v) {
            $conteudo .= $k . " => " . $v . "\n";
        }

        $conteudo.= "hash => {$hash} \n";

        return $logFile->write($conteudo);
    }

}
