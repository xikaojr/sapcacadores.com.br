<?php

class Atleta extends Devel_Db_Table_Abstract {

    const ATIVO = 1;
    const INATIVO = 2;
    const MACHUCADO = 3;
    const TECNICO = 4;

    protected $_name = 'atleta';
    protected $_alias = 'atl';
    protected $_primary = 'id';

    public function getList($params) {
//                    --, COUNT(DISTINCT t.id) total_treinos

        $sql = " SELECT 
                    a.*
                    ,SUM(CASE WHEN t.data >= a.criado_em THEN 1 ELSE 0 END) total_treinos
                    ,CASE WHEN a.criado_em >= '2015-04-12' THEN 'T' ELSE 'F' END novato
                    ,SUM(CASE WHEN att.id IS NOT NULL THEN 1 ELSE 0 END) qtd_presenca
                   FROM atleta a
                   INNER JOIN treino t ON 1=1
                   LEFT JOIN atleta_treino att ON att.treino_id = t.id AND a.id = att.atleta_id
                   WHERE 1=1
                   GROUP BY a.nome "
//                . (isset($params["id"]) && !empty($params["id"]) ? " AND {$this->_alias}.id = " . $this->quote($params["id"]) : "")
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

    public function getAtivos($params) {

        $sql = " SELECT 
                *
                FROM {$this->_name} AS {$this->_alias}
            WHERE 1=1 ";
//            AND situacao = " . self::ATIVO ;

        if (isset($params['order']) && !empty($params['order'])) {
            $sql .= " ORDER BY {$params['order']}";
        }

//                . (isset($params["id"]) && !empty($params["id"]) ? " AND {$this->_alias}.id = " . $this->quote($params["id"]) : "")
//                . (isset($params["trabalho_id"]) && !empty($params["trabalho_id"]) ? " AND tb.trabalho_id = " . $this->quote($params["trabalho_id"]) : "")
//                . (isset($params["nome"]) && !empty($params["nome"]) ? " AND {$this->_alias}.nome iLIKE " . $this->quote("%{$params["nome"]}%") : "")
//                . (isset($params["nome_cracha"]) && !empty($params["nome_cracha"]) ? " AND {$this->_alias}.nome_cracha iLIKE " . $this->quote("%{$params["nome_cracha"]}%") : "")
//                . (isset($params["cpf"]) && !empty($params["cpf"]) ? " AND {$this->_alias}.cpf = " . $this->quote(Zend_Filter_Digits::filter($params['cpf'])) : "")
//                . (isset($params["rg"]) && !empty($params["rg"]) ? " AND {$this->_alias}.rg = " . $this->quote($params['rg']) : "")
//                . (isset($params["email"]) && !empty($params["email"]) ? " AND {$this->_alias}.email LIKE " . $this->quote($params['email']) : "")
//                . (isset($params["entidade_id"]) && !empty($params["entidade_id"]) ? " AND ent.id = " . $this->quote($params['entidade_id']) : "")
//                . (isset($params["order"]) && !empty($params["order"]) ? " \nORDER BY " . $params["order"] : "\nORDER BY {$this->_alias}.id DESC")
//                . (isset($params["limit"]) && !empty($params["limit"]) ? " LIMIT " . $this->quote($params["limit"]) : "")
//                . (isset($params["limit"]) && !empty($params["limit"]) && (isset($params["offset"]) && !empty($params["offset"])) ? " OFFSET " . $this->quote($params["offset"]) : "");

        return $this->getAdapter()->query($sql)->fetchAll();
    }

}
