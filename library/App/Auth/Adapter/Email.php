<?php

class App_Auth_Adapter_Email extends App_Auth_Adapter_DbTable {

    protected $_tableName = 'usuarios';
    protected $_campoEmail = 'email';
    protected $_email;

    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }
    
    public function setSenha($senha) {
        $this->setCredential($senha);
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getCampoEmail() {
        return $this->_campoEmail;
    }

    public function authenticate() {
        $this->setIdentityColumn($this->getCampoEmail())->setIdentity($this->getEmail());
        return parent::authenticate();
    }

    public function _authenticateCreateSelect() {
        $select = parent::_authenticateCreateSelect();
        $select->where("{$this->_zendDb->quoteIdentifier($this->getCampoEmail(), true)} = ?", $this->getEmail());
        return $select;
    }

}