<?php

class Municipios extends Devel_Db_Table_Abstract {

    protected $_name = 'municipios';
    protected $_primary = "id";

    public function getByUf($uf) {

        if (!isset($uf) || empty($uf)) {
            throw new Exception("Voce precisa informa o UF da cidade :: MunicipioTable : getByUf");
        }

        $uf = strtoupper($uf);

        $sql = "select * from municipios where uf = '{$uf}' order by descricao asc";
        return $this->getAdapter()->query($sql)->fetchAll();
    }
    
    public function getByDescricao($descricao) {

        if (!isset($descricao) || empty($descricao)) {
            throw new Exception("Voce precisa informa a cidade!");
        }

        $sql = "select * from municipios where upper(unaccent(descricao)) = upper(unaccent('{$descricao}')) ";
        return $this->getAdapter()->query($sql)->fetch();
    }

    public function getList($params = array()) {

        $sql = "SELECT *, m.id municipio_id, (m.descricao || '-' || m.uf) descricao_f
                FROM municipios m
                WHERE 1=1 "
                . (!empty($params["id"]) ? " AND m.id = " . $this->quote($params["id"]) : "")
                . (!empty($params["cod_uf"]) ? " AND m.uf ILIKE " . $this->quote('%' . $params["cod_uf"] . '%') : "")
                . (!empty($params["nome"]) ? " AND m.descricao ILIKE " . $this->quote('%' . $params["nome"] . '%') : "")
                . " ORDER BY m.uf,m.descricao "
                . (!empty($params["cod_uf"]) ? '':" ASC LIMIT 100 ");
//        die($sql);
        $result = $this->getAdapter()->fetchAll($sql);
//        Zend_Debug::dump($result);die("Municipios Model");
        return $result;
    }

    public function get($params = array()) {

        $sql = "SELECT *, m.id municipio_id, (m.descricao || '-' || m.uf) descricao_f
                FROM municipios m
                WHERE 1=1 "
                . (isset($params["id"]) ? " AND m.id = " . $this->quote($params["id"]) : "");
        $result = $this->getAdapter()->fetchRow($sql);

        return $result;
    }

}
