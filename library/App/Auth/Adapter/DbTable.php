<?php

/**
 * Description of DbTable
 *
 * @author Sidney
 */
class App_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable {

    protected $_credentialColumn = 'senha';

    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $this->_authenticateSetup();
        $dbSelect = $this->_authenticateCreateSelect();
        $resultIdentities = $this->_authenticateQuerySelect($dbSelect);

        if (($authResult = $this->_authenticateValidateResultSet($resultIdentities)) instanceof Zend_Auth_Result) {
            return $authResult;
        }

        if (true === $this->getAmbiguityIdentity()) {
            $validIdentities = array();
            $zendAuthCredentialMatchColumn = $this->_zendDb->foldCase('zend_auth_credential_match');
            foreach ($resultIdentities as $identity) {
                if (1 === (int) $identity[$zendAuthCredentialMatchColumn]) {
                    $validIdentities[] = $identity;
                }
            }
            $resultIdentities = $validIdentities;
        }

        $authResult = $this->_authenticateValidateResult(array_shift($resultIdentities));
        return $authResult;
    }

}
