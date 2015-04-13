<?php

class App_Auth_Adapter_EmailCpf extends App_Auth_Adapter_DbTable {

    protected $_tableName = 'pessoas';
    protected $_campoEmail = 'email';
    protected $_email;
    protected $_campoCpf = 'cpf';
    protected $_cpf;

    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }

    public function setCpf($cpf) {
        $filter = new Zend_Filter_Digits();
        $this->_cpf = $filter->filter($cpf);
        return $this;
    }

    public function setSenha($senha) {
        $this->setCredential($senha);
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getCpf() {
        return $this->_cpf;
    }

    public function getCampoEmail() {
        return $this->_campoEmail;
    }

    public function getCampoCpf() {
        return $this->_campoCpf;
    }

    public function authenticate() {
        $this->setIdentityColumn($this->getCampoEmail())->setIdentity($this->getEmail());
        return parent::authenticate();
    }

    public function _authenticateCreateSelect() {
        $select = parent::_authenticateCreateSelect();
        $select->where("{$this->_zendDb->quoteIdentifier($this->getCampoEmail(), true)} = ?", $this->getEmail());

        if ($this->getCpf()) {
            $select->orWhere("{$this->_zendDb->quoteIdentifier($this->getCampoCpf(), true)} = ?", $this->getCpf());
        }
        return $select;
    }

}
