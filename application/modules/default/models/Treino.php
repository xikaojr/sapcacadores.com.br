<?php

class Treino extends Devel_Db_Table_Abstract {

    protected $_name = 'treino';
    protected $_alias = 'tr';
    protected $_primary = 'id';

    public function getList($params) {

        $sql = " SELECT 
                {$this->_alias}.*
                ,lc.descricao as local
                ,DATE_FORMAT(data, '%d/%m/%Y') as data
                FROM {$this->_name} AS {$this->_alias}
                INNER JOIN local as lc ON lc.id = tr.local_id
            WHERE 1=1 "
            . (isset($params["id"]) && !empty($params["id"]) ? " AND {$this->_alias}.id = " . $this->quote($params["id"]) : "")
//                . (isset($params["trabalho_id"]) && !empty($params["trabalho_id"]) ? " AND tb.trabalho_id = " . $this->quote($params["trabalho_id"]) : "")
//                . (isset($params["nome"]) && !empty($params["nome"]) ? " AND {$this->_alias}.nome iLIKE " . $this->quote("%{$params["nome"]}%") : "")
//                . (isset($params["nome_cracha"]) && !empty($params["nome_cracha"]) ? " AND {$this->_alias}.nome_cracha iLIKE " . $this->quote("%{$params["nome_cracha"]}%") : "")
//                . (isset($params["cpf"]) && !empty($params["cpf"]) ? " AND {$this->_alias}.cpf = " . $this->quote(Zend_Filter_Digits::filter($params['cpf'])) : "")
//                . (isset($params["rg"]) && !empty($params["rg"]) ? " AND {$this->_alias}.rg = " . $this->quote($params['rg']) : "")
//                . (isset($params["email"]) && !empty($params["email"]) ? " AND {$this->_alias}.email LIKE " . $this->quote($params['email']) : "")
//                . (isset($params["entidade_id"]) && !empty($params["entidade_id"]) ? " AND ent.id = " . $this->quote($params['entidade_id']) : "")
            . (isset($params["order"]) && !empty($params["order"]) ? " \nORDER BY " . $params["order"] : "\nORDER BY {$this->_alias}.id DESC");
//                . (isset($params["limit"]) && !empty($params["limit"]) ? " LIMIT " . $this->quote($params["limit"]) : "")
//                . (isset($params["limit"]) && !empty($params["limit"]) && (isset($params["offset"]) && !empty($params["offset"])) ? " OFFSET " . $this->quote($params["offset"]) : "");
        
        return $this->getAdapter()->query($sql)->fetchAll();
    }

}
