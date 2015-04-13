<?php

class App_Auth_Adapter_Cpf extends Itarget_Auth_Adapter {

    protected $_tableName = 'pessoas';
    protected $_campoCpf = 'cpf';
    protected $_cpf;

    public function setCpf($cpf) {
        $filter = new Zend_Filter_Digits();
        $this->_cpf = $filter->filter($cpf);
        return $this;
    }

    public function setSenha($senha) {
        $this->setCredential($senha);
        return $this;
    }

    public function getCpf() {
        return $this->_cpf;
    }

    public function getCampoCpf() {
        return $this->_campoCpf;
    }

    public function authenticate() {
        $this->setIdentityColumn($this->getCampoCpf())->setIdentity($this->getCpf());
        return parent::authenticate();
    }

    public function _authenticateCreateSelect() {
        $select = parent::_authenticateCreateSelect();
        $select->where("{$this->getCampoCpf()} IS NOT NULL AND {$this->_zendDb->quoteIdentifier($this->getCampoCpf(), true)} = ?", $this->getCpf());
        return $select;
    }

}