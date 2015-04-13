<?php

class Itarget_CartaoCredito_Amex extends Itarget_CartaoCredito {
    const VERSAO = '1';
    const URL_PRODUCAO = 'https://vpos.amxvpos.com/vpcpay';
    const IDIOMA_PT = 'pt_BR';
    const IDIOMA_EN = 'en';
    const IDIOMA_ES = 'es';

    const PARCELAMENTO_LOJA = 'PlanN';
    const PARCELAMENTO_ADMINISTRADORA = 'PlanAmex';

    const PAGAMENTO_CREDITO = 'CREDIT';
    const PAGAMENTO_DEBITO = 'DEBIT';

    /**
     * @param int $tipoParcelamento
     * @return Amex
     */
    public function setTipoParcelamento($tipoParcelamento) {
        if ($tipoParcelamento == '3') {
            $tipoParcelamento = self::PARCELAMENTO_ADMINISTRADORA;
        } else {
            $tipoParcelamento = self::PARCELAMENTO_LOJA;
        }

        $this->tipoParcelamento = $tipoParcelamento;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getTipoParcelamento() {
        return $this->tipoParcelamento;
    }

    /**
     * @var string vpc_Hash
     */
    private $chave;

    /**
     * @var string vpc_ReturnURL
     */
    private $urlRetorno;

    /**
     *
     * @var string
     */
    private $versao;

    /**
     * @var string vpc_Locale
     */
    private $idioma;

    /**
     * @var string vpc_MerchTxnRef
     */
    private $nossoNumero;

    /**
     * @var string vpc_AccessCode
     */
    private $accessCode;

    /**
     * @var string vpc_Command (pay)
     */
    private $comando;

    /**
     * @var string vpc_OrderInfo
     */
    private $descricaoTransacao;

    /**
     *
     * @var int
     */
    private $numeroParcelas;

    /**
     *
     * @param string $afiliacao vpc_Merchant
     */
    public function __construct($afiliacao = null) {
        parent::__construct($afiliacao);
        $this->setVersao(self::VERSAO);
    }

    public function getChave() {
        return $this->chave;
    }

    public function setChave($chave) {
        $this->chave = $chave;
        return $this;
    }

    public function getUrlRetorno() {
        return $this->urlRetorno;
    }

    public function setUrlRetorno($urlRetorno) {
        $this->urlRetorno = $urlRetorno;
        return $this;
    }

    public function getVersao() {
        return $this->versao;
    }

    public function setVersao($versao) {
        $this->versao = $versao;
        return $this;
    }

    public function setAfiliacao($afiliacao) {
        $this->afiliacao = $afiliacao;
        return $this;
    }

    public function setTid($tid) {
        $this->tid = $tid;
        return $this;
    }

    /**
     *
     * @param type $valor vpc_Amount
     * @return Itarget_CartaoCredito_Amex
     */
    public function setValorTransacao($valor) {
        $this->valorTransacao = number_format($valor, 2, '', '');
        return $this;
    }

    public function getIdioma() {
        return $this->idioma;
    }

    public function setIdioma($idioma) {
        $this->idioma = $idioma;
        return $this;
    }

    public function getNossoNumero() {
        return $this->nossoNumero;
    }

    public function setNossoNumero($nossoNumero) {
        $this->nossoNumero = $nossoNumero;
        return $this;
    }

    public function getAccessCode() {
        return $this->accessCode;
    }

    public function setAccessCode($accessCode) {
        $this->accessCode = $accessCode;
        return $this;
    }

    public function getComando() {
        return $this->comando;
    }

    public function setComando($comando) {
        $this->comando = $comando;
        return $this;
    }

    public function getDescricaoTransacao() {
        return $this->descricaoTransacao;
    }

    public function setDescricaoTransacao($descricaoTransacao) {
        $descricaoTransacao = substr(strip_tags(trim($descricaoTransacao)), 0, 33);
        $this->descricaoTransacao = $descricaoTransacao;
        return $this;
    }

    /**
     * Inicia o processo de pagamento
     * @return amex
     */
    protected function requisicao($url, array $dados, $tipo) {

        $res = parent::requisicao($url, $dados);

        if (null !== $this->getLog()) {
            $this->getLog()->write("{$tipo}:\n" . end($dados));
            $this->getLog()->write("RESPOSTA:\n" . $res);
        }

        return $res;
    }

    public function setUrlAutenticacao($urlAutenticacao) {
        $this->urlAutenticacao = $urlAutenticacao;
        return $this;
    }

    public function getUrlAutenticacao() {
        return $this->urlAutenticacao;
    }

    public function realizarTransacao($comando = 'pay') {
        $this->setComando($comando);

        $html = '
            <form name="form_cartao" id="form_cartao" method="POST" action="' . $this->getWebservice() . '">
                <input type="hidden" name="vpc_Hash" value="' . $this->getChave() . '">
                <input type="hidden" name="vpc_Merchant" value="' . $this->getAfiliacao() . '">
                <input type="hidden" name="vpc_Version" value="' . $this->getVersao() . '">
                <input type="hidden" name="vpc_Command" value="' . $this->getComando() . '">
                <input type="hidden" name="vpc_AccessCode" value="' . $this->getAccessCode() . '">
                <input type="hidden" name="vpc_MerchTxnRef" value="' . $this->getNossoNumero() . '">
                <input type="hidden" name="vpc_Amount" value="' . $this->getValorTransacao() . '">
                <input type="hidden" name="vpc_Locale" value="' . $this->getIdioma() . '">
                <input type="hidden" name="vpc_OrderInfo" value="' . $this->getDescricaoTransacao() . '">
                <input type="hidden" name="vpc_ReturnURL" value="' . $this->getUrlRetorno() . '">
                <input type="hidden" name="vpc_numPayments" value="' . $this->getNumeroParcelas() . '">
                <input type="hidden" name="vpc_PaymentMethod" value="' . $this->getFormaPagamento() . '">
            </form>
        ';
        
        $this->setUrlAutenticacao($html);

        return $this;
    }

    public function getNumeroParcelas() {
        return $this->numeroParcelas;
    }

    public function setNumeroParcelas($numeroParcelas) {
        $this->numeroParcelas = $numeroParcelas;
        return $this;
    }

    /**
     *
     * @param string|int $formaPagamento
     * @return Amex
     */
    public function setFormaPagamento($formaPagamento) {
        $this->formaPagamento = $formaPagamento;
        return $this;
    }

    /**
     * @return string codigo da forma de pagamento
     */
    public function getFormaPagamento() {
        return $this->formaPagamento;
    }

    public function capturarTransacao() {
        return false;
    }

    public function gerarTid() {
        return false;
    }

//    public static function getResultDescription($responseCode) {
//        $translate = Zend_Registry::get('translate');
//
//        switch ($responseCode) {
//            case "0" : $result = $translate->_('Transaction Successful');
//                break;
//            case "?" : $result = $translate->_('Transaction status is unknown');
//                break;
//            case "E" : $result = $translate->_('Referred');
//                break;
//            case "1" : $result = $translate->_('Transaction Declined');
//                break;
//            case "2" : $result = $translate->_('Bank Declined Transaction');
//                break;
//            case "3" : $result = $translate->_('No Reply from Bank');
//                break;
//            case "4" : $result = $translate->_('Expired Card');
//                break;
//            case "5" : $result = $translate->_('Insufficient funds');
//                break;
//            case "6" : $result = $translate->_('Error Communicating with Bank');
//                break;
//            case "7" : $result = $translate->_('Payment Server detected an error');
//                break;
//            case "8" : $result = $translate->_('Transaction Type Not Supported');
//                break;
//            case "9" : $result = $translate->_('Bank declined transaction (Do not contact Bank)');
//                break;
//            case "A" : $result = $translate->_('Transaction Aborted');
//                break;
//            case "C" : $result = $translate->_('Transaction Cancelled');
//                break;
//            case "D" : $result = $translate->_('Deferred transaction has been received and is awaiting processing');
//                break;
//            case "F" : $result = $translate->_('3D Secure Authentication failed');
//                break;
//            case "I" : $result = $translate->_('Card Security Code verification failed');
//                break;
//            case "L" : $result = $translate->_('Shopping Transaction Locked (Please try the transaction again later)');
//                break;
//            case "N" : $result = $translate->_('Cardholder is not enrolled in Authentication scheme');
//                break;
//            case "P" : $result = $translate->_('Transaction has been received by the Payment Adaptor and is being processed');
//                break;
//            case "R" : $result = $translate->_('Transaction was not processed - Reached limit of retry attempts allowed');
//                break;
//            case "S" : $result = $translate->_('Duplicate SessionID (Amex Only)');
//                break;
//            case "T" : $result = $translate->_('Address Verification Failed');
//                break;
//            case "U" : $result = $translate->_('Card Security Code Failed');
//                break;
//            case "V" : $result = $translate->_('Address Verification and Card Security Code Failed');
//                break;
//            default : $result = $translate->_('Unable to be determined');
//        }
//        return $result;
//    }

}