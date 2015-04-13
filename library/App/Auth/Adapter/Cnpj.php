<?php

class App_Auth_Adapter_Cnpj extends Itarget_Auth_Adapter {

    protected $_tableName = 'pessoas';
    protected $_campoCnpj = 'cnpj';
    protected $_cnpj;

    public function setCnpj($cnpj) {
        $this->_cnpj = $cnpj;
        return $this;
    }

    public function setSenha($senha) {
        $this->setCredential(sha1($senha));
        return $this;
    }

    public function getCnpj() {
        return $this->_cnpj;
    }

    public function getCampoCnpj() {
        return $this->_campoCnpj;
    }

    public function authenticate() {
        $this->setIdentityColumn($this->getCampoCnpj())->setIdentity($this->getCnpj());
        return parent::authenticate();
    }

    public function _authenticateCreateSelect() {
        $select = parent::_authenticateCreateSelect();
        $select->where("{$this->_zendDb->quoteIdentifier($this->getCampoCnpj(), true)} = ?", $this->getCnpj());
        return $select;
    }

}