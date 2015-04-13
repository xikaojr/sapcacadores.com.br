<?php

class Pais extends Devel_Db_Table_Abstract {

    protected $_name = 'paises';

    public function findByDescricao($descricao) {
        $registros = array();

        $select = $this->select()
                ->where("descricao ilike(?)", "%{$descricao}%");

        return $this->fetchAll($select)->toArray();
    }

    public function getPaises() {
        $cacheName = md5('cache-' . $this->_name);

        if (($result = $this->_cache->load($cacheName)) === false) {
            $sql = "select * from {$this->_name} order by descricao";
            $result = $this->getDefaultAdapter()->query($sql)->fetchAll();
            $this->_cache->save($result, $cacheName);
        }

        return $result;
    }

}
