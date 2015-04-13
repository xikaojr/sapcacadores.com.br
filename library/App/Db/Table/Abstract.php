<?php

class App_Db_Table_Abstract extends Zend_Db_Table_Abstract {

    const REGEXP_DATA = '/^(\d{4})-(\d{2})-(\d{2})$/';
    const REGEXP_DATA_BR = '/^(\d{2})\/(\d{2})\/(\d{4})$/';
    const REGEXP_VALIDAR_DATA = '/^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))\/(19|20)?\d{4}$/';
    const REGEXP_CPF = '/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9ˆ]{2})$/';
    const REGEXP_NUMERO = '/\d/';
    const REGEXP_SOMENTE_NUMEROS = '/[^̣\d]/';
    const REGEXP_SOMENTE_TEXTO = '/[^̣[:alpha:]]/';
    const REGEXP_SOMENTE_TEXTO_NUMERO = '/[^̣a-zA-Z0-9]/';
    const ZEND_DATE_PT = 'dd/MM/yyyy';
    const ZEND_DATE_EN = 'yyyy-MM-dd';
    const ZEND_TIMESTAMP_PT = 'dd/MM/yyyy HH:mm:ss';
    const ZEND_TIMESTAMP_EN = 'yyyy-MM-dd HH:mm:ss';

    protected $_usuario;

    public function init() {
        $auth = Zend_Auth::getInstance();
        $this->_usuario = $auth->getStorage()->read();

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

//    public function save(array $dados) {
//        $primary_key = $this->_primary;
//
//        caso seja especificado as colunas da tabela sera relaizado um filtro no parametros
//
//        if (isset($this->_cols) && is_array($this->_cols)) {
//            foreach ($dados as $param_name => $param_value) {
//                if (!in_array($param_name, $this->_cols)) {
//                    unset($dados[$param_name]);
//                }
//            }
//        }
//
//        //monta o where para o UPDATE
//        if (is_array($primary_key)) {
//            foreach ($primary_key as $field_name) {
//                if (isset($dados[$field_name]) && !empty($dados[$field_name])) {
//                    $and[] = $this->getAdapter()->quoteInto("{$field_name} = ? ", $dados[$field_name]);
//                } else {
//                    unset($dados[$field_name]);
//                }
//            }
//        } else {
//            if (isset($dados[$primary_key]) && !empty($dados[$primary_key])) {
//                $and[] = $this->getAdapter()->quoteInto("{$primary_key} = ? ", $dados[$primary_key]);
//            } else {
//                unset($dados[$primary_key]);
//            }
//        }
//
//        if (isset($and) && !empty($and)) {
//            $where = implode(' AND ', $and);
//        }
//        //echo $where;
//        // fim da montagem do where para o UPDATE
//        $dados_ = array();
//        foreach ($dados as $index => $key) {
//            if (empty($key) && !is_numeric($key)) {
//                $dados_[$index] = new Zend_Db_Expr("NULL");
//            } else {
//                $dados_[$index] = $key;
//            }
//        }
//        $dados = $dados_;
//
//        try {
//
//            if (isset($where) && !empty($where)) {
//
//                $num_registro_afetados = self::update($dados, $where);
//
//                if (is_array($primary_key)) {
//                    foreach ($primary_key as $field_name) {
//                        if (isset($dados[$field_name]) && !empty($dados[$field_name])) {
//                            $retorno[$field_name] = $dados[$field_name];
//                        }
//                    }
//                } else {
//                    return $dados[$primary_key];
//                }
//            } else {
//                $retorno = self::insert($dados);
//            }
//        } catch (Exception $exc) {
//            throw new App_Db_Exception($exc->getMessage()
//            , $exc->getCode(), App_Db_Exception::TYPE_INSERT_OR_UPDATE);
//        }
//
//        return $retorno;
//    }

    public function delete($where) {
        try {
            parent::delete($where);
        } catch (Exception $exc) {
            throw new App_Db_Exception($exc->getMessage()
            , $exc->getCode(), App_Db_Exception::TYPE_DELETE);
        }
    }

    protected function quoteInto($sql, $value) {
        return $this->getAdapter()->quoteInto($sql, $value);
    }

    protected function quote($value) {
        return $this->getAdapter()->quote($value);
    }

    public function converteDatasToing($dados = array(), $prefx, $regex) {

        foreach ($dados as $key => $value) {

            if (strpos($key, $prefx) !== false) {
                if (!empty($dados[$key])) {
//                    $dados[$key] = Itarget_Date::enEn($dados[$key]);
                    $dados[$key] = preg_replace($regex, '$3-$2-$1', $dados[$key]);
                } else {
                    $dados[$key] = null;
                }
            }
        }

        return $dados;
    }

}
