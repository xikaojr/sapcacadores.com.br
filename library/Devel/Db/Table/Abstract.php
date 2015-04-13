<?php

class Devel_Db_Table_Abstract extends Zend_Db_Table_Abstract {

    protected $_usuario;
    protected $_cache;

    public function init() {
        $auth = Zend_Auth::getInstance();
        $this->_usuario = $auth->getStorage()->read();
        $this->_cache = Zend_Registry::get('cache');
        parent::init();
    }

    public function createRow(array $data = array(), $defaultSource = null) {
        try {
            return parent::createRow($data, $defaultSource);
        } catch (Exception $exc) {
            throw new App_Db_Exception($exc->getMessage()
            , $exc->getCode(), App_Db_Exception::TYPE_INSERT_OR_UPDATE);
        }
    }

    public function save(array $data = array()) {

        try {

            $data['criado_por'] = $this->_usuario->id;

            $this->_primary = "id";
            if (isset($data[$this->_primary]) && !empty($data[$this->_primary])) {
                $row = $this->fetchRow("id = " . $data[$this->_primary]);
                $row->setFromArray($data);
            } else {
                unset($data[$this->_primary]);
                $row = $this->createRow($data);
            }

            $row->save();
            return $row;
        } catch (Exception $exc) {
            throw new App_Db_Exception($exc->getMessage()
            , $exc->getCode(), App_Db_Exception::TYPE_INSERT_OR_UPDATE);
        }
    }

    protected function quoteInto($sql, $value) {
        return $this->getAdapter()->quoteInto($sql, $value);
    }

    protected function quote($value) {
        return $this->getAdapter()->quote($value);
    }

    /**
     * Verifica se o campo existe
     * @param string $tabela tabela 
     * @param string $campo campo 
     * @return bool
     */
    public function campoExiste($tabela, $campo) {

        $sql = "
            SELECT a.relname as tabela, b.attname AS campo
            FROM pg_class a
            JOIN pg_attribute b ON (b.attrelid = a.relfilenode)
            WHERE  b.attstattarget = -1 AND
           " . $this->quoteInto("a.relname = ?", $tabela) . " AND  " . $this->quoteInto("b.attname = ?", $campo);
        return $this->getAdapter()->query($sql)->fetch();
    }

    public function queryPersonalida($dados) {
        $select = $this->select()->setIntegrityCheck(false);

        if (isset($dados['fields']) && is_array($dados['fields']))
            $select->from(array($this->_name), $dados['fields']);
        else
            $select->from(array($this->_name), array("*"));

        if (isset($dados['join']) && !empty($dados['join'])) {
            foreach ($dados['join'] as $j) {
                $select->join($j["tabela"], $j["condicao"], $j["fields"]);
            }
        }

        if (isset($dados['order']) && !empty($dados['order'])) {
            $select->order($dados['order']);
        }
        if (isset($dados['where']) && !empty($dados['where'])) {
            $select->where($dados['where']);
        }

        $rs = array();
        $rs['total'] = $this->fetchAll($select)->count();

        if (isset($dados['limit']) && !empty($dados['limit'])) {
            $select->limitPage($dados['limit'][0], $dados['limit'][1]);
        }

        $rs['dados'] = $rs['total'] > 0 ? $this->fetchAll($select)->toArray() : array();

        return $rs;
    }

    /**
     * Retorna todos os registros de uma tabela
     * @return array
     */
    public function getAll($order = null) {

        if (!empty($this->_schema)) {
            $table = $this->_schema . "." . $this->_name;
        } else {
            $table = $this->_name;
        }

        $sql = "SELECT * FROM {$table}";

        if ($order)
            $sql .= " ORDER BY {$order}";

        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

}
