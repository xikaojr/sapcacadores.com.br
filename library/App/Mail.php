<?php

class App_Mail extends Zend_Mail {

    public function __construct($charset = 'UTF-8') {

        parent::__construct($charset);
        $config = Zend_Registry::get('config');
        $usuarios = $config->smtp->params->username->toArray();
        $senhas = $config->smtp->params->password->toArray();
        $sorteado = rand(0, count($usuarios) - 1);

        $mailConfig = array();
        $mailConfig['auth'] = $config->smtp->params->auth;
        $mailConfig['username'] = $usuarios[$sorteado];
        $mailConfig['password'] = $senhas[$sorteado];

        if (isset($config->smtp->params->port)) {
            $mailConfig['port'] = $config->smtp->params->port;
        }

        $this->setDefaultTransport(new Zend_Mail_Transport_Smtp($config->smtp->server, $mailConfig));
    }

    public function assunto($assunto) {
        $this->setSubject($assunto);
        return $this;
    }

    public function de($email, $name = '') {
        $this->setFrom($email, $name);
        return $this;
    }

    public function para($email, $name = '') {
        $this->addTo($email, $name);
        return $this;
    }

    public function copia($email, $name = '') {
        $this->addCc($email, $name);
        return $this;
    }

    public function copiaOculta($email, $name = '') {
        $this->addBcc($email, $name);
        return $this;
    }

    public function responderPara($email, $name = '') {
        return $this->setReplyTo($email, $name);
        return $this;
    }

    public function texto($texto, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
        $this->setBodyText(strip_tags($texto), $charset, $encoding);
        return $this;
    }

    public function html($texto, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
        $this->setBodyHtml($texto, $charset, $encoding);
        return $this;
    }

    public function enviar($transport = null) {
        return $this->send($transport);
    }
    
    public function mensagem($texto, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
        return $this->texto($texto, $charset, $encoding)->html($texto, $charset, $encoding);
    }
    
    public function anexo($arquivo, $nomeArquivo, $tipoArquivo) {
        $at = $this->createAttachment(file_get_contents($arquivo));
        
        if ($tipoArquivo == 'pdf') {
            $at->type = 'application/pdf';
            $at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $at->encoding = Zend_Mime::ENCODING_BASE64;
            $at->filename = $nomeArquivo;
        }
        
        return $at;
    }

}
