<?php

class Usuarios extends Devel_Db_Table_Abstract {

    protected $_name = "usuarios";
    protected $_alias = "usr";
    protected $_primary = "id";

    //ID DO ADM EM TODOS OS SISTEMAS TEM QUE SER 1.
    const ADMINISTRADOR = 1;

    public function getInstance() {
        return new Usuarios();
    }

    /**
     * Busca simples por usuários
     * 
     * @param sql $where
     * @return array
     */
    public function getUsuarios($where = '1=1') {
        $sql = "select * from {$this->_name} where {$where}";
        $result = $this->getDefaultAdapter()->query($sql)->fetchAll();

        return $result;
    }

    /**
     * Verifica se o login já existe
     * 
     * @param string $login
     * @return boolean
     */
    public function loginExiste($login) {
        $select = $this->select()->where('login = ?', $login)->limit(1);

        $rs = $this->fetchAll($select)->toArray();

        if (!empty($rs)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verificar se existe cadastro de acordo com o paramentro que for passado
     * @param array $params - Paramentros para ser buscado
     * @return boolean
     */
    public function existUser($where = '1=1') {
        $sql = "SELECT id FROM pessoas WHERE {$where}";

        $rs = $this->getAdapter()->query($sql)->fetchAll();

        if (count($rs) > 0) {
            return $rs[0]['id'];
        }

        return false;
    }

    public function getList($params) {

        $sql = " SELECT * FROM {$this->_name} WHERE 1=1 "
                . (isset($params["id"]) && !empty($params["id"]) ? " AND id = " . $this->quote($params["id"]) : "")
                . (isset($params["descricao"]) && !empty($params["descricao"]) ? " AND descricao iLIKE " . $this->quote("%{$params["descricao"]}%") : "")
                . (isset($params["order"]) && !empty($params["order"]) ? " \nORDER BY " . $params["order"] : "\nORDER BY loc_a.id DESC")
                . (isset($params["limit"]) && !empty($params["limit"]) ? " LIMIT " . $this->quote($params["limit"]) : "")
                . (isset($params["limit"]) && !empty($params["limit"]) && (isset($params["offset"]) && !empty($params["offset"])) ? " OFFSET " . $this->quote($params["offset"]) : "");

        return $this->getAdapter()->query($sql)->fetchAll();
    }

    public function getListCount($params) {

        $sql = " SELECT COUNT(1) FROM {$this->_name} WHERE 1=1 "
                . (isset($params["id"]) && !empty($params["id"]) ? " AND id = " . $this->quote($params["id"]) : "")
                . (isset($params["descricao"]) && !empty($params["descricao"]) ? " AND descricao iLIKE " . $this->quote("%{$params["descricao"]}%") : "");

        return $this->getAdapter()->fetchOne($sql);
    }

    public function autentica($email, $senha) {
        $authAdapter = $this->getAuthAdapter($email, $senha);
        $auth = App_Auth::getInstance(App_Controller_Default::SESSION_STORAGE);
        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {

            $data = $authAdapter->getResultRowObject(null, 'senha');

            Usuarios::getInstance()->update(array(
                'ultimo_login' => DATA_HORA_ATUAL
                    ), "id = {$data->id}");

            $auth->getStorage()->write($data);
            return true;
        } else {
            throw new Exception('Dados de acesso inválidos!');
        }
    }

    private function getAuthAdapter($email, $senha) {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('usuarios');
        $authAdapter->setIdentityColumn('email');
        $authAdapter->setCredentialColumn('senha');
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($senha);
        $authAdapter->setCredentialTreatment("MD5(?)");
        return $authAdapter;
    }

}
